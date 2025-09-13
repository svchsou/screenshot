<?php
namespace App\Http\Controllers;

use App\Models\Screenshot;
use App\Services\StorageRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class ViewController extends Controller
{
    public function show($slug)
    {
        $shot = Screenshot::where('slug', $slug)->firstOrFail();
        if ($shot->expires_at && $shot->expires_at->isPast()) {
            abort(404);
        }
        $shot->increment('views_count');
        $imageUrl = StorageRouter::getUrl($shot->disk, $shot->path, $shot->slug);
        $thumbUrl = StorageRouter::getUrl($shot->disk, preg_replace('/\.([a-zA-Z0-9]+)$/', '.thumb.webp', $shot->path), $shot->slug);
        $deleteToken = request('dtoken');
        return view('show', compact('shot', 'imageUrl', 'thumbUrl', 'deleteToken'));
    }

    public function raw($slug)
    {
        $shot = Screenshot::where('slug', $slug)->firstOrFail();
        if (in_array($shot->disk, ['s3', 'spaces'])) {
            return Redirect::away(Storage::disk($shot->disk)->temporaryUrl($shot->path, now()->addMinutes(5)));
        }
        if ($shot->disk === 'ftp') {
            // Fallback: stream via controller
            $stream = Storage::disk('ftp')->readStream($shot->path);
            return Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => $shot->mime,
                'Cache-Control' => 'public, max-age=31536000',
                'ETag' => md5($shot->path),
            ]);
        }
        // Local/public
        return Redirect::away(Storage::disk($shot->disk)->url($shot->path));
    }
}
