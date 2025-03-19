<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
        'status',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    // علاقة مع القسم
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // علاقة مع المستخدم (ناشر المقال)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع التاغات
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }
}
