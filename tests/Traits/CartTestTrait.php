<?php

namespace WebDevRus\LaravelCart\Tests\Traits;

use WebDevRus\LaravelCart\Facades\Cart;
use WebDevRus\LaravelCart\Providers\CartServiceProvider;

trait CartTestTrait
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CartServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Cart' => Cart::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('session.driver', 'array');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        (new \WebDevRus\LaravelCart\Tests\Database\migrations\UserMigration)->up();
        (new \WebDevRus\LaravelCart\Tests\Database\migrations\CartMigration)->up();
    }

    private function cart()
    {
        for ($i = 1; $i <= $this->count; $i++) {
            Cart::add([
                'product_id' => $i,
                'quantity' => rand(1, 5),
                'price' => rand(100, 10000),
            ]);
        }

        return Cart::get();
    }

    private function cartAdd(int $quantity)
    {
        $cart = $this->getCart(function () use ($quantity) {
            Cart::add([
                'product_id' => $this->count + 1,
                'quantity' => $quantity,
                'price' => 100,
            ]);
        });

        $this->assertCount($this->count + 1, $cart['items']);

        return $cart;
    }

    private function getLastItem(array $cart): array
    {
        $index = array_key_last($cart['items']);

        return [
            'item' => $cart['items'][$index],
            'index' => $index,
        ];
    }

    private function getCart(?callable $callback = null, ?array $cart = null)
    {
        if (is_null($cart)) {
            $cart = $this->cart();
        }

        if (is_callable($callback)) {
            $callback($cart);
        }

        return Cart::get();
    }
}
