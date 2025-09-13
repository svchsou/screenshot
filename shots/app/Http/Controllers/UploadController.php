<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreScreenshotRequest;
use App\Models\StorageDestination;
use App\Services\StorageRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UploadController extends Controller
{
    public function index()
    {
        $maxMb = env('UPLOAD_MAX_MB', 12);
        $allowed = explode(',', env('ALLOWED_MIMES', 'image/png,image/jpeg,image/webp'));
        return view('upload', compact('maxMb', 'allowed'));
    }

    public function store(StoreScreenshotRequest $request)
    {
        $file = $request->file('image');
        $dest = StorageDestination::where('is_default', true)->first();
        $shot = StorageRouter::storeUploadedImage($file, $dest);
        return Redirect::route('screenshots.show', ['slug' => $shot->slug, 'dtoken' => $shot->delete_token]);
    }
}
