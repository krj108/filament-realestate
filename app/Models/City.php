<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id',
        'name',
        'slug',
    ];

    // علاقة المدينة بمحافظتها
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    // // توليد الـ slug من الاسم تلقائيًا
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $model->slug = \Str::slug($model->name);
    //     });
    // }
}
