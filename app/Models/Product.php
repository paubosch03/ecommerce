<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'featured', 'active',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'featured' => 'boolean',
        'active'   => 'boolean',
    ];

    // Un producto pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Un producto puede estar en muchos pedidos
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Un producto puede estar en muchos carritos
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}