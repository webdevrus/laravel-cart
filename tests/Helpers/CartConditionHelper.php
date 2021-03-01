<?php

namespace WebDevRus\LaravelCart\Tests\Helpers;

use WebDevRus\LaravelCart\Helpers\Helper;

class CartConditionHelper extends Helper
{
    public function condition(array $item, array $data): bool
    {
        return $item['product_id'] === $data['product_id']
            && $item['option_id'] === $data['option_id'];
    }
}
