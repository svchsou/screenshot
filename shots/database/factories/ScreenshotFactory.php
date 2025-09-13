<?php
namespace Database\Factories;

use App\Models\Screenshot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ScreenshotFactory extends Factory
{
    protected $model = Screenshot::class;

    public function definition(): array
    {
        $uuid = $this->faker->uuid;
        $slug = Str::random(8);
        return [
            'uuid' => $uuid,
            'slug' => $slug,
            'disk' => 'public',
            'path' => '2024/01/01/'.$uuid.'.png',
            'mime' => 'image/png',
            'size_bytes' => $this->faker->numberBetween(10000, 2000000),
            'width' => $this->faker->numberBetween(100, 1920),
            'height' => $this->faker->numberBetween(100, 1080),
            'views_count' => 0,
            'ip_hash' => hash('sha256', $this->faker->ipv4),
            'expires_at' => null,
            'delete_token' => Str::random(32),
        ];
    }
}
