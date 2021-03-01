<?php

namespace WebDevRus\LaravelCart\Services\Drivers;

use WebDevRus\LaravelCart\Services\Driver;

class Database extends Driver
{
    private $user;

    protected function boot()
    {
        $this->user = auth()->user();

        $this->merge();
    }

    public function get()
    {
        $items = $this->user->cart()->first()->data ?? null;

        if (!is_null($items)) {
            return $this->output($items);
        }

        return null;
    }

    public function item(int $index)
    {
        if (config('cart.item_index_decrement')) {
            $index--;
        }

        $items = $this->user->cart()->first()->data ?? null;

        if (!is_null($items) && array_key_exists($index, $items)) {
            return $this->itemOutput($items[$index]);
        }

        return null;
    }

    public function add(array $data): void
    {
        $cart = $this->user->cart()->first()->data ?? null;

        if (!is_null($cart)) {
            $status = $this->each($cart, $data, function ($i, $data) use (&$cart) {
                $cart[$i]['quantity'] = $cart[$i]['quantity'] + $data['quantity'];
                return true;
            });

            if (!$status) {
                $cart[] = $data;
            }
        } else {
            $cart = [$data];
        }

        $this->user->cart()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['data' => $cart]
        );
    }

    public function remove(array $data): void
    {
        $cart = $this->user->cart()->first()->data ?? null;

        if (!is_null($cart)) {
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

            $cart = array_values($cart);

            $this->user->cart()->updateOrCreate(
                ['user_id' => $this->user->id],
                ['data' => $cart]
            );
        }
    }

    public function clear(): void
    {
        if (!is_null($this->user->cart)) {
            $this->user->cart->delete();
        }
    }

    private function merge(): void
    {
        if (config('cart.merge') && session()->has('cart')) {
            $cart = session()->get('cart');
            foreach ($cart as $item) {
                $this->add($item);
            }
            session()->forget('cart');
            session()->save();
        }
    }
}