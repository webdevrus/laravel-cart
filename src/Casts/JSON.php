<?php

namespace WebDevRus\LaravelCart\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JSON implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value, true);
    }

    public function set($model, $key, $value, $attributes): string
    {
        return json_encode($value);
    }
}
