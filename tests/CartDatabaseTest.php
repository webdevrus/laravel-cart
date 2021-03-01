<?php

namespace WebDevRus\LaravelCart\Tests;

use Illuminate\Support\Facades\Auth;
use WebDevRus\LaravelCart\Tests\CartTest;
use WebDevRus\LaravelCart\Tests\Models\User;

class CartDatabaseTest extends CartTest
{
    protected function auth(): void
    {
        Auth::login(User::create());
    }
}