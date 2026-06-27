<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
    ];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    // Add inside any model class
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
