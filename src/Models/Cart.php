<?php

namespace WebDevRus\LaravelCart\Models;

use WebDevRus\LaravelCart\Casts\JSON;
use Illuminate\Database\Eloquent\Model;
use WebDevRus\Laravel\UUID;

class Cart extends Model
{
    use UUID;

    protected $table = 'cart';

    protected $fillable = [
        'user_id', 'data'
    ];

    protected $casts = [
        'data' => JSON::class,
    ];
}
