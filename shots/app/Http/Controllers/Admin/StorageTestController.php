<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorageTestController extends Controller
{
    public function test(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:local,ftp,s3,spaces',
            'credentials' => 'required|array',
        ]);
        try {
            $res = \App\Services\StorageRouter::validateConfig($data['type'], $data['credentials']);
            return response()->json($res);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'messages' => [$e->getMessage()]], 200);
        }
    }
}

