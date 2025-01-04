<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'status', 'type', 'default_image', 'stock'];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
