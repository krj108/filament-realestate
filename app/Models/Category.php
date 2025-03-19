<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'image'];

    // علاقة بالقسم الأب
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // علاقة بالأقسام الفرعية
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
