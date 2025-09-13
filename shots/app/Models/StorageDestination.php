<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'credentials', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
