<?php

namespace WebDevRus\LaravelCart\Tests;

use WebDevRus\LaravelCart\Facades\Cart;
use WebDevRus\LaravelCart\Tests\Traits\CartTestTrait;

abstract class CartTest extends TestCase
{
    use CartTestTrait;

    private $count = 10;

    /** @test */
    public function it_empty_cart()
    {
        $this->assertNull(Cart::get());
    }
    
    /** @test */
    public function adding_products_to_cart()
    {
        $cart = $this->cart();

        $this->assertNotEmpty($cart);
        $this->assertCount($this->count, $cart['items']);
    }

    /** @test */
    public function remove_items_from_cart()
    {
        $cart = $this->getCart(function () {
            Cart::remove([
                'product_id' => rand(1, 10),
            ]);
        });

        $this->assertCount($this->count - 1, $cart['items']);
    }

    /** @test */
    public function it_clear_cart()
    {
        $cart = $this->getCart(function () {
            Cart::clear();
        });

        $this->assertNull($cart);
    }

    /** @test */
    public function get_item_by_index()
    {
        $cart = $this->getCart(function () {
            $this->app['config']->set('cart.item_index_decrement', true);
        });

        for ($i = 0; $i < $this->count; $i++) {
            $this->assertEquals($cart['items'][$i], Cart::item($i+1));
        }
    }

    /** @test */
    public function decrement_item_quantity()
    {
        $cart = $this->cartAdd(2);
        extract($this->getLastItem($cart));

        $cart = $this->getCart(function () use ($item) {
            Cart::remove([
                'product_id' => $item['product_id'],
                'quantity' => -1,
            ]);
        });

        $this->assertTrue($cart['items'][$index]['quantity'] < $item['quantity']);
        $this->assertEquals($cart['items'][$index]['quantity'], $item['quantity'] - 1);
    }

    /** @test */
    public function remove_item_after_decrement_quantity()
    {
        $cart = $this->cartAdd(1);
        extract($this->getLastItem($cart));

        $cart = $this->getCart(function () use ($item) {
            Cart::remove([
                'product_id' => $item['product_id'],
                'quantity' => -1,
            ]);
        });

        $this->assertCount($this->count, $cart['items']);
    }

    /** @test */
    public function get_cart_driver_name_as_database_or_session()
    {
        $this->assertSame(Cart::getDriverName(), config('cart.driver') ?? auth()->check() ? 'database' : 'session');
    }

    /** @test */
    public function cart_to_object()
    {
        $cart = $this->getCart(function () {
            $this->app['config']->set('cart.output', 'object');
        });

        $this->assertIsObject($cart);
    }

    /** @test */
    public function cart_to_json()
    {
        $cart['array'] = $this->cart();
        $cart['json'] = $this->getCart(function () {
            $this->app['config']->set('cart.output', 'json');
            $this->app['config']->set('cart.' . Cart::getDriverName() . '.helper', \WebDevRus\LaravelCart\Tests\Helpers\CartJsonHelper::class);
        }, $cart['array']);

        $this->assertIsString($cart['json']);
        $this->assertJsonStringEqualsJsonString(json_encode($cart['array']), $cart['json']);
    }

    /** @test */
    public function cart_condition_with_option()
    {
        $this->app['config']->set('cart.' . Cart::getDriverName() . '.helper', \WebDevRus\LaravelCart\Tests\Helpers\CartConditionHelper::class);

        $iterations = 2;

        for ($i = 0; $i < $iterations; $i++) {
            Cart::add([
                'product_id' => 10,
                'option_id' => 2,
                'quantity' => 1,
                'price' => 1000,
            ]);
        }

        Cart::add([
            'product_id' => 10,
            'option_id' => 1,
            'quantity' => 1,
            'price' => 1000,
        ]);

        $cart = Cart::get();

        $this->assertCount(2, $cart['items']);
        $this->assertEquals($cart['items'][0]['quantity'], $iterations);
        $this->assertEquals($cart['items'][1]['quantity'], 1);
    }

    /** @test */
    public function checking_total_values()
    {
        $cart = $this->cart();
        $totals = ['count', 'price', 'quantity'];

        foreach ($totals as $total) {
            $this->assertArrayHasKey('total_' . $total, $cart);
        }
    }

    /** @test */
    public function checking_total_price()
    {
        $cart = $this->cart();

        $total = array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart['items']);

        $this->assertEquals($cart['total_price'], array_sum($total));
    }

    /** @test */
    public function checkint_total_quantity()
    {
        $cart = $this->cart();

        $total = array_map(function ($item) {
            return $item['quantity'];
        }, $cart['items']);

        $this->assertEquals($cart['total_quantity'], array_sum($total));
    }

    /** @test */
    public function checking_total_count()
    {
        $cart = $this->cart();

        $this->assertEquals(count($cart['items']), $cart['total_count']);
    }
}