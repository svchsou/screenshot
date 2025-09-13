<?php
namespace Database\Factories;

use App\Models\StorageDestination;
use Illuminate\Database\Eloquent\Factories\Factory;

class StorageDestinationFactory extends Factory
{
    protected $model = StorageDestination::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company.' Storage',
            'type' => $this->faker->randomElement(['local', 'ftp', 's3', 'spaces']),
            'credentials' => encrypt(json_encode(['root' => '/tmp'])),
            'is_default' => false,
        ];
    }
}
