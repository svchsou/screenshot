<?php
namespace Tests\Feature;

use App\Models\Screenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_command_deletes_expired_screenshot()
    {
        Storage::fake('public');
        $shot = Screenshot::factory()->create([
            'disk' => 'public',
            'path' => '2024/01/01/foo.png',
            'expires_at' => now()->subDay(),
        ]);
        Storage::disk('public')->put($shot->path, 'dummy');
        Artisan::call('screenshots:purge-expired');
        $this->assertDatabaseMissing('screenshots', ['id' => $shot->id]);
        Storage::disk('public')->assertMissing($shot->path);
    }
}
