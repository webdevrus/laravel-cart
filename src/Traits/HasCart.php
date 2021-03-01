<?php

namespace WebDevRus\LaravelCart\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasCart
{
    public function cart(): HasOne
    {
        return $this->hasOne(config('cart.database.model'));
    }
}