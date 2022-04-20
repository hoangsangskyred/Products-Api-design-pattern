<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='products';
    
    protected $fillable = [
        'name',
        'slug',
        'color',
        'quantity',
        'is_active'
    ];

    public function getIsActiveAttribute(): string
    {
        return $this->attributes['is_active'] == 0 ? 'Inactive' : 'Active';
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
}
