<?php
namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class DeleteController extends Controller
{
    public function destroy(Request $request, $slug)
    {
        $shot = Screenshot::where('slug', $slug)->firstOrFail();
        $token = $request->input('delete_token') ?? $request->input('dtoken');
        if ($token !== $shot->delete_token) {
            return back()->withErrors(['delete_token' => 'Invalid delete token.']);
        }
        // Delete original and thumb
        Storage::disk($shot->disk)->delete([$shot->path, preg_replace('/\.([a-zA-Z0-9]+)$/', '.thumb.webp', $shot->path)]);
        $shot->delete();
        return Redirect::route('upload.form')->with('status', 'Screenshot deleted.');
    }
}
