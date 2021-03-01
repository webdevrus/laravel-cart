<?php

if (!function_exists('cart')) {
    function cart() {
        $service = config('cart.service');
        $service = new $service();

        return $service->class;
    }
}