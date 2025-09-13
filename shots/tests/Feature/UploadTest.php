<?php
namespace Tests\Feature;

use App\Models\Screenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_image_upload_creates_screenshot_and_thumbnail()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.png', 600, 400)->size(500);
        $response = $this->post('/upload', [
            'image' => $file,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseCount('screenshots', 1);
        $shot = Screenshot::first();
        Storage::disk('public')->assertExists($shot->path);
        Storage::disk('public')->assertExists(preg_replace('/\.([a-zA-Z0-9]+)$/', '.thumb.webp', $shot->path));
    }

    public function test_invalid_mime_is_rejected()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('test.txt', 10, 'text/plain');
        $response = $this->post('/upload', [
            'image' => $file,
        ]);
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('screenshots', 0);
    }

    public function test_oversize_image_is_rejected()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('big.png')->size(13000); // 13MB
        $response = $this->post('/upload', [
            'image' => $file,
        ]);
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('screenshots', 0);
    }
}
