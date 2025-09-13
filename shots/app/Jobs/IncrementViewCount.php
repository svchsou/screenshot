<?php
namespace App\Jobs;

use App\Models\Screenshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IncrementViewCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shotId;

    public function __construct($shotId)
    {
        $this->shotId = $shotId;
    }

    public function handle()
    {
        if (!env('QUEUE_VIEW_COUNT', false)) {
            return;
        }
        Screenshot::where('id', $this->shotId)->increment('views_count');
    }
}
