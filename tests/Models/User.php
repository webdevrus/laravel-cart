<?php

namespace WebDevRus\LaravelCart\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use WebDevRus\LaravelCart\Traits\HasCart;

class User extends Authenticatable
{
    use HasCart;
}