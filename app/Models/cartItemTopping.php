<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartItemTopping extends Pivot
{
    public $timestamps = true;

    protected $table = 'cart_item_toppings';
}
