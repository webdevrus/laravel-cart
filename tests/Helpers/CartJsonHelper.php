<?php

namespace WebDevRus\LaravelCart\Tests\Helpers;

use WebDevRus\LaravelCart\Helpers\Helper;

class CartJsonHelper extends Helper
{
    public function toJson(array $array): string
    {
        return json_encode($array);
    }
}