<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boxes extends Model
{
    use HasFactory;

/**
 * The attributes that are mass assignable.
 *
 * @var array<int, string>
 */
protected $fillable = [
    'user_id',
    'name',
    'description',
    'address',
    'price',
    'status',
];

/**
 * The attributes that should be cast.
 *
 * @return array<string, string>
 */
protected function casts(): array
{
    return [
        'status' => 'boolean',
    ];
}

/**
 * Get the user that owns the box.
 */
public function user()
{
    return $this->belongsTo(User::class);
}
}
