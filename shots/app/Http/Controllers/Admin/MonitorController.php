<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screenshot;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class MonitorController extends Controller
{
    public function index()
    {
        $byDisk = Screenshot::query()
            ->selectRaw('disk, COUNT(*) as count, SUM(size_bytes) as total')
            ->groupBy('disk')
            ->get();
        $total = Screenshot::sum('size_bytes');

        $logFile = storage_path('logs/laravel.log');
        $logs = file_exists($logFile) ? $this->tail($logFile, 200) : 'No logs yet.';

        return view('admin.monitor.index', compact('byDisk', 'total', 'logs'));
    }

    public function purgeExpired()
    {
        Artisan::call('screenshots:purge-expired');
        return Redirect::route('admin.monitor.index')->with('status', trim(Artisan::output()) ?: 'Purge executed.');
    }

    protected function tail(string $filepath, int $lines = 200): string
    {
        $f = fopen($filepath, 'r');
        $buffer = '';
        $pos = -1;
        $readLines = 0;
        fseek($f, 0, SEEK_END);
        $filesize = ftell($f);
        while ($readLines < $lines && -$pos < $filesize) {
            fseek($f, $pos, SEEK_END);
            $char = fgetc($f);
            if ($char === "\n") { $readLines++; }
            $buffer = $char.$buffer;
            $pos--;
        }
        fclose($f);
        return $buffer;
    }
}

