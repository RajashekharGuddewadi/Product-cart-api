<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_active'];

    /**
     * Relationship: Cart belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Cart has many CartItems
     * Using eager loading: Cart::with('items.product')
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the active cart for a user (or create if doesn't exist)
     */
    public static function getActiveCart($userId)
    {
        return Cart::with(['items.product'])->where('user_id', $userId)->where('is_active', true)->first();
    }

    /**
     * Calculate total price of cart
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
    }
}
