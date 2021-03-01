<?php

namespace WebDevRus\LaravelCart\Services\Drivers;

use WebDevRus\LaravelCart\Services\Driver;

class Session extends Driver
{
    public function get()
    {
        if (session()->has('cart')) {
            return $this->output(session()->get('cart'));
        }

        return null;
    }

    public function item(int $index)
    {
        if (config('cart.item_index_decrement')) {
            $index--;
        }

        if (session()->has('cart')) {
            return $this->itemOutput(session()->get("cart.{$index}"));
        }

        return null;
    }

    public function add(array $data): void
    {
        if (!session()->has('cart')) {
            session()->put([
                'cart' => [
                    $data
                ],
            ]);
        } else {
            $cart = session()->get('cart');

            $status = $this->each($cart, $data, function ($i, $data) use (&$cart) {
                $cart[$i]['quantity'] = $cart[$i]['quantity'] + $data['quantity'];
                return true;
            });

            if (!$status) {
                $cart[] = $data;
            }

            session()->put([
                'cart' => $cart,
            ]);
        }
    }

    public function remove(array $data): void
    {
        if (session()->has('cart')) {
            $cart = session()->get('cart');

            $this->each($cart, $data, function ($i, $data) use (&$cart) {
                if (isset($data['quantity']) && $data['quantity'] < 0 && $cart[$i]['quantity'] > 1) {
                    $cart[$i]['quantity']--;
                } else {
                    unset($cart[$i]);
                }
            });

            if (empty($cart)) {
                $this->clear();
                return;
            }

            session()->put([
                'cart' => $cart,
            ]);
        }
    }

    public function clear(): void
    {
        if (session()->has('cart')) {
            session()->forget('cart');
            session()->save();
        }
    }
}