<?php

namespace WebDevRus\LaravelCart\Helpers;

class Helper
{
    /**
     * @var object
     */
    public $total;

    public function condition(array $item, array $data): bool
    {
        return $item['product_id'] === $data['product_id'];
    }

    public function total(array $items): self
    {
        $total = [
            'count' => count($items),
            'price' => 0,
            'quantity' => 0,
        ];

        foreach ($items as $item) {
            $total['price'] += $item['price'] * $item['quantity'];
            $total['quantity'] += $item['quantity'];
        }

        $this->total = (object) $total;

        return $this;
    }

    public function toObject(array $array): object
    {
        return json_decode(json_encode($array));
    }
}