<?php

return [

    'service' => \WebDevRus\LaravelCart\Services\CartService::class,

    /**
     * Available Drivers: "session", "database"
     */

    'session' => [
        'driver' => \WebDevRus\LaravelCart\Services\Drivers\Session::class,
        'helper' => \WebDevRus\LaravelCart\Helpers\Helper::class,
    ],

    'database' => [
        'model'  => \WebDevRus\LaravelCart\Models\Cart::class,
        'driver' => \WebDevRus\LaravelCart\Services\Drivers\Database::class,
        'helper' => \WebDevRus\LaravelCart\Helpers\Helper::class,
    ],

    /**
     * Supported: "array", "object", custom (see documentation)
     */
    'output' => 'array',

    /**
     * Merge cart after switching driver "session" -> "database"
     * 
     * boolean
     */
    'merge' => true,

    'item_index_decrement' => true,

];