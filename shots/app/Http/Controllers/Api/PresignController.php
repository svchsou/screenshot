<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PresignController extends Controller
{
    public function presign(Request $request)
    {
        $disk = $request->input('disk', 's3');
        $filename = $request->input('filename', Str::random(16) . '.png');
        $datePath = now()->format('Y/m/d');
        $key = "$datePath/" . Str::uuid() . "." . pathinfo($filename, PATHINFO_EXTENSION);
        $client = Storage::disk($disk)->getDriver()->getAdapter()->getClient();
        $bucket = config('filesystems.disks.' . $disk . '.bucket');
        $expires = 300;
        $post = $client->createPresignedPost([
            'Bucket' => $bucket,
            'Key' => $key,
            'ACL' => 'private',
            'Content-Type' => $request->input('mime', 'image/png'),
        ], [
            ['content-length-range', 0, env('UPLOAD_MAX_MB', 12) * 1024 * 1024],
        ], '+5 minutes');
        return response()->json([
            'url' => $post['url'],
            'fields' => $post['fields'],
            'key' => $key,
        ]);
    }
}
