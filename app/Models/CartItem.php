<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price_at_time'];

    /**
     * Relationship: CartItem belongs to Cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relationship: CartItem belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
