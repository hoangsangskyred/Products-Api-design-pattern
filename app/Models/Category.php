<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='categories';

    protected $fillable = [
        'name',
        'slug',
        'parent',
        'is_active'
    ];
    public function getIsActiveAttribute(): string
    {
        return $this->attributes['is_active'] == 0 ? 'Inactive' : 'Active';
    }
    public function categories()
    {
        return $this->hasMany(Category::class, 'parent');
    }

    public function childrenCategory()
    {
        return $this->categories()->with('childrenCategory');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'category_product');
    }
    
}
