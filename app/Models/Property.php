<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'price', 'content', 'featured_image', 'image_gallery',
        'location', 'type', 'bedrooms', 'area', 'bathrooms', 'status',
        'meta_title', 'meta_description', 'meta_keywords', 'user_id'
    ];
    public function isPublished()
    {
        return $this->status === 'published';
    }
    protected $casts = [
        'image_gallery' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            $property->slug = Str::slug($property->name);
            if (auth()->check()) {
                $property->user_id = auth()->id(); 
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
