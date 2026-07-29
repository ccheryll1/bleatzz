<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuTopping extends Pivot
{
    public $timestamps = false;

    protected $table = 'menu_toppings';
}
