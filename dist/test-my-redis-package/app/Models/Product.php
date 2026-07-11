<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'created_at',
        'updated_at',
        'title',
        'slug',
        'image',
        'stock',
        'price',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'title' => 'string',
        'slug' => 'string',
        'image' => 'string',
        'stock' => 'integer',
        'price' => 'float',
    ];
}
