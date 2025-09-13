<?php
namespace Tests\Feature;

use App\Models\StorageDestination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class StorageDestinationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_spaces_destination_and_validate()
    {
        $creds = [
            'key' => 'test',
            'secret' => 'test',
            'region' => 'ap-southeast-1',
            'bucket' => 'bucket',
            'endpoint' => 'https://sgp1.digitaloceanspaces.com',
            'url' => '',
            'use_path_style' => false,
        ];
        $dest = StorageDestination::create([
            'name' => 'Spaces',
            'type' => 'spaces',
            'credentials' => Crypt::encryptString(json_encode($creds)),
            'is_default' => true,
        ]);
        $this->assertEquals('spaces', $dest->type);
        $this->assertTrue($dest->is_default);
    }
}
