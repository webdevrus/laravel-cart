<?php

namespace WebDevRus\LaravelCart\Services;

use Closure;

abstract class Driver
{
    abstract public function get();
    abstract public function item(int $index);
    abstract public function add(array $data);
    abstract public function remove(array $data);
    abstract public function clear();

    public function __construct(string $driver)
    {
        $this->driver = $driver;

        $this->getModel();
        $this->getHelper();

        if (method_exists($this, 'boot')) {
            call_user_func([$this, 'boot']);
        }
    }
    
    /**
     * Get Driver name for Facade
     *
     * @return string
     */
    final public function getDriverName(): string
    {
        return $this->driver;
    }

    final protected function each(array $cart, array $data, callable $callback)
    {
        foreach ($cart as $i => $item) {
            if ($this->helper->condition($item, $data)) {
                return $callback($i, $data);
            }
        }
    }

    public function output(array $items)
    {
        $total = $this->helper->total($items)->total;

        if (method_exists($this->helper, 'before')) {
            $this->helper->before($items);
        }

        $cart = [
            'items' => $items,
        ];

        foreach ($total as $k => $v) {
            $cart["total_{$k}"] = $v;
        }

        if (method_exists($this->helper, 'to' . ucfirst(config('cart.output')))) {
            $cart = $this->helper->{'to' . ucfirst(config('cart.output'))}($cart);
        }
        
        if (method_exists($this->helper, 'after')) {
            $this->helper->after($cart);
        }

        return $cart;
    }

    public function itemOutput(array $item)
    {
        if (method_exists($this->helper, 'itemBefore')) {
            $this->helper->itemBefore($item);
        }

        if (method_exists($this->helper, 'to' . ucfirst(config('cart.output')))) {
            $item = $this->helper->{'to' . ucfirst(config('cart.output'))}($item);
        }

        if (method_exists($this->helper, 'itemAfter')) {
            $this->helper->itemAfter($item);
        }

        return $item;
    }

    /**
     * Get Cart Model Class
     *
     * @return void
     */
    private function getModel(): void
    {
        $model = config("cart.{$this->driver}.model");

        if (class_exists($model)) {
            $this->model = new $model;
        }
    }

    /**
     * Get Helper Class
     *
     * @return void
     */
    private function getHelper(): void
    {
        $helper = config("cart.{$this->driver}.helper");

        if (is_bool($helper) && !$helper) {
            return;
        }

        CartService::inspection($helper);

        $this->helper = new $helper();
    }
}