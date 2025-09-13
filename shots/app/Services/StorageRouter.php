<?php
namespace App\Services;

use App\Models\Screenshot;
use App\Models\StorageDestination;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as ImageManager;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StorageRouter
{
    /**
     * Get the default disk name from DB or env
     */
    public static function getDefaultDisk(): string
    {
        $default = StorageDestination::where('is_default', true)->first();
        if ($default) {
            return $default->type === 'spaces' ? 'spaces' : $default->type;
        }
        return env('DEFAULT_UPLOAD_DISK', 'public');
    }

    /**
     * Build a FilesystemAdapter for a given StorageDestination
     */
    public static function forDestination(StorageDestination $dest): FilesystemAdapter
    {
        $creds = json_decode(Crypt::decryptString($dest->credentials), true);
        return self::buildAdapter($dest->type, $creds);
    }

    /** Build a FilesystemAdapter from type + credentials array */
    public static function buildAdapter(string $type, array $creds): FilesystemAdapter
    {
        switch ($type) {
            case 'local':
                return new FilesystemAdapter(
                    app('filesystem')->createLocalDriver(['root' => $creds['root'] ?? storage_path('app')])
                );
            case 'ftp':
                if (!extension_loaded('ftp')) {
                    throw new \RuntimeException('PHP ftp extension is not enabled. Enable it in your php.ini (extension=ftp).');
                }
                if (!class_exists(\League\Flysystem\Ftp\FtpAdapter::class)) {
                    throw new \RuntimeException('FTP adapter not installed. Run: composer require league/flysystem-ftp:^3.0');
                }
                return new FilesystemAdapter(
                    app('filesystem')->createFtpDriver($creds)
                );
            case 's3':
            case 'spaces':
                $config = [
                    'driver' => 's3',
                    'key' => $creds['key'] ?? null,
                    'secret' => $creds['secret'] ?? null,
                    'region' => $creds['region'] ?? null,
                    'bucket' => $creds['bucket'] ?? null,
                    'endpoint' => $creds['endpoint'] ?? null,
                    'url' => $creds['url'] ?? null,
                    'root' => $creds['root'] ?? null,
                    'use_path_style_endpoint' => $creds['use_path_style'] ?? false,
                ];
                return new FilesystemAdapter(
                    app('filesystem')->createS3Driver($config)
                );
        }
        throw new \Exception('Unknown storage type');
    }

    /**
     * Validate connectivity and basic R/W for a destination.
     */
    public static function validateDestination(StorageDestination $dest): array
    {
        $creds = json_decode(Crypt::decryptString($dest->credentials), true);
        return self::validateConfig($dest->type, $creds);
    }

    /** Validate connectivity and basic R/W for arbitrary config (no DB) */
    public static function validateConfig(string $type, array $creds): array
    {
        $messages = [];
        $ok = true;

        // Preflight checks per type for clearer error messages
        if ($type === 'ftp') {
            foreach (['host','username','password'] as $k) {
                if (empty($creds[$k])) { $ok = false; $messages[] = "Missing FTP $k"; }
            }
            if (!extension_loaded('ftp')) {
                $ok = false; $messages[] = 'PHP ftp extension is not enabled (enable extension=ftp).';
            }
            if (!class_exists(\League\Flysystem\Ftp\FtpAdapter::class)) {
                $ok = false; $messages[] = 'FTP adapter missing. Run: composer require league/flysystem-ftp:^3.0';
            }
        } elseif ($type === 'local') {
            $root = $creds['root'] ?? storage_path('app');
            if (!is_dir($root)) { $ok = false; $messages[] = 'Root not found: '.$root; }
            if ($ok && !is_writable($root)) { $ok = false; $messages[] = 'Root not writable: '.$root; }
        } elseif (in_array($type, ['s3','spaces'])) {
            foreach (['key','secret','region','bucket'] as $k) {
                if (empty($creds[$k])) { $ok = false; $messages[] = "Missing $type $k"; }
            }
            if (!class_exists(\Aws\S3\S3Client::class)) {
                $ok = false; $messages[] = 'AWS SDK not installed. Run: composer require aws/aws-sdk-php:^3.0';
            }
            if (!class_exists(\League\Flysystem\AwsS3V3\AwsS3V3Adapter::class)) {
                $ok = false; $messages[] = 'S3 Flysystem adapter missing. Run: composer require league/flysystem-aws-s3-v3:^3.0';
            }
            if (!extension_loaded('openssl')) { $messages[] = 'Warning: openssl extension not enabled (required for secure connections).'; }
            if (!extension_loaded('curl')) { $messages[] = 'Warning: curl extension not enabled (AWS SDK uses it for HTTP).'; }
        }

        if (!$ok) {
            return ['ok' => false, 'messages' => $messages];
        }

        try {
            $fs = self::buildAdapter($type, $creds);
            $testPath = 'connectivity-tests/'.uniqid('ping-').'.txt';
            $fs->put($testPath, 'ok:'.now());
            if (!$fs->exists($testPath)) { $ok = false; $messages[] = 'Write verification failed.'; }
            $size = $fs->size($testPath);
            $messages[] = 'Wrote test file ('.$size.' bytes).';
            $fs->delete($testPath);
            $messages[] = 'Cleanup OK.';
            if (in_array($type, ['s3','spaces'])) {
                $messages[] = 'Bucket: '.($creds['bucket'] ?? 'n/a').' Region: '.($creds['region'] ?? 'n/a');
                if (isset($creds['root'])) { $messages[] = 'Prefix: '.$creds['root']; }
            }
        } catch (\Throwable $e) {
            $ok = false;
            $messages[] = $e->getMessage();
        }
        return ['ok' => $ok, 'messages' => $messages];
    }

    /**
     * Generate a public URL for a file on a given disk
     */
    public static function getUrl(string $disk, string $path, ?string $slug = null): string
    {
        $cdn = env('CDN_BASE_URL');
        if (in_array($disk, ['local', 'public'])) {
            return $cdn ? rtrim($cdn, '/').'/'.$path : Storage::disk($disk)->url($path);
        }
        if ($disk === 'ftp') {
            if ($cdn) { return rtrim($cdn, '/').'/'.$path; }
            // For FTP we can't expose a direct URL reliably; stream via controller using slug
            if ($slug) {
                return URL::route('screenshots.raw', ['slug' => $slug]);
            }
            return '';
        }
        if (in_array($disk, ['s3', 'spaces'])) {
            if ($cdn) {
                return rtrim($cdn, '/').'/'.$path;
            }
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5));
        }
        return '';
    }

    /**
     * Store uploaded image, validate, make thumbnail, persist Screenshot
     */
    public static function storeUploadedImage(UploadedFile $file, ?StorageDestination $dest = null): Screenshot
    {
        // Server-side MIME detection
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);
        $allowed = explode(',', env('ALLOWED_MIMES', 'image/png,image/jpeg,image/webp'));
        $maxMb = (int) env('UPLOAD_MAX_MB', 12);
        if (!in_array($realMime, $allowed)) {
            throw new \Exception('Invalid file type.');
        }
        if ($file->getSize() > $maxMb * 1024 * 1024) {
            throw new \Exception('File too large.');
        }
        // Use Intervention Image to get dimensions
        $img = \Intervention\Image\Laravel\Facades\Image::read($file->getRealPath());
        $width = $img->width();
        $height = $img->height();
        // Generate UUID, slug, delete_token
        $uuid = (string) \Illuminate\Support\Str::uuid();
        $slug = app(\App\Actions\GenerateSlug::class)->handle();
        $delete_token = \Illuminate\Support\Str::random(32);
        // Sanitize extension
        $ext = strtolower($file->extension());
        if (!in_array($ext, ['png','jpg','jpeg','webp'])) {
            $ext = match($realMime) {
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                default => 'bin',
            };
        }
        $datePath = now()->format('Y/m/d');
        $filename = "$uuid.$ext";
        $thumbname = "$uuid.thumb.webp";
        $path = "$datePath/$filename";
        $thumbPath = "$datePath/$thumbname";
        // Choose disk
        $disk = $dest ? ($dest->type === 'spaces' ? 'spaces' : $dest->type) : self::getDefaultDisk();
        $storage = $dest ? self::forDestination($dest) : \Illuminate\Support\Facades\Storage::disk($disk);
        // Store original (set ACL private for S3/Spaces)
        $options = [];
        if (in_array($disk, ['s3','spaces'])) {
            $options['visibility'] = 'private';
        } else {
            $options['visibility'] = 'public';
        }
        $storage->put($path, file_get_contents($file->getRealPath()), $options);
        // Make and store thumbnail (max 1200px width, webp)
        $thumb = $img->scale(width: min(1200, $width))->toWebp();
        $storage->put($thumbPath, $thumb, $options);
        // Persist DB
        $shot = \App\Models\Screenshot::create([
            'uuid' => $uuid,
            'slug' => $slug,
            'disk' => $disk,
            'path' => $path,
            'mime' => $realMime,
            'size_bytes' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'ip_hash' => hash_hmac('sha256', request()->ip(), config('app.key')),
            'delete_token' => $delete_token,
        ]);
        return $shot;
    }
}
