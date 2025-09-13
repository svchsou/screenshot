<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'slug', 'disk', 'path', 'mime', 'size_bytes',
        'width', 'height', 'views_count', 'ip_hash', 'expires_at', 'delete_token'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'views_count' => 'integer',
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];
}
