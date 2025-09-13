<?php
namespace Tests\Feature;

use App\Models\Screenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_route_increments_view_counter()
    {
        $shot = Screenshot::factory()->create(['views_count' => 0]);
        $this->get('/' . $shot->slug);
        $this->assertEquals(1, $shot->fresh()->views_count);
    }

    public function test_raw_route_redirects_for_s3()
    {
        $shot = Screenshot::factory()->create(['disk' => 's3', 'path' => 'foo/bar.png']);
        Storage::shouldReceive('disk')->with('s3')->andReturnSelf();
        Storage::shouldReceive('temporaryUrl')->andReturn('https://s3-url');
        $response = $this->get('/' . $shot->slug . '/raw');
        $response->assertRedirect('https://s3-url');
    }
}
