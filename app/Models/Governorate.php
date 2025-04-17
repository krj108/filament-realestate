<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // علاقة المحافظات بالمدن
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // توليد الـ slug من الاسم تلقائيًا
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $model->slug = \Str::slug($model->name);
    //     });
    // }
}
