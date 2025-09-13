<?php
namespace App\Console\Commands;

use App\Models\Screenshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PurgeExpiredScreenshots extends Command
{
    protected $signature = 'screenshots:purge-expired';
    protected $description = 'Delete expired screenshots and their files from storage and DB.';

    public function handle()
    {
        $expired = Screenshot::whereNotNull('expires_at')->where('expires_at', '<', now())->get();
        $count = 0;
        $byDisk = [];
        foreach ($expired as $shot) {
            Storage::disk($shot->disk)->delete([$shot->path, preg_replace('/\.([a-zA-Z0-9]+)$/', '.thumb.webp', $shot->path)]);
            $byDisk[$shot->disk] = ($byDisk[$shot->disk] ?? 0) + 1;
            $shot->delete();
            $count++;
        }
        foreach ($byDisk as $disk => $cnt) {
            Log::info("Purged $cnt expired screenshots from $disk");
        }
        $this->info("Purged $count expired screenshots.");
    }
}
