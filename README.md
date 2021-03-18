# Laravel E-Commerce / Cart

<p align="center"><img src="https://github.com/webdevrus/webdevrus/blob/master/assets/laravel-cart.png?raw=true" alt="Laravel E-Commerce / Cart"></p>

## [Русский](README.ru.md) | English

| Cart | Laravel |
| ---- | ------- |
| 2.x  | 8.x     |
| 1.x  | 7.x     |

## Installation
```console
$ composer require webdevrus/laravel-cart
```

1. Migrations
```console
$ php artisan vendor:publish --provider="WebDevRus\LaravelCart\Providers\CartServiceProvider" --tag="migrations"
```
```console
$ php artisan migrate
```
2. Configuration
```console
$ php artisan vendor:publish --provider="WebDevRus\LaravelCart\Providers\CartServiceProvider" --tag="config"
```
3. Specify trait `HasCart` in model `User`.
```php
<?php

namespace App\Models;

use WebDevRus\LaravelCart\Traits\HasCart;

class User extends Authenticatable
{
    use HasCart;

    ...
}
```
## Usage
```php
use WebDevRus\LaravelCart\Facades\Cart;

...

// Add item
Cart::add([
    'product_id' => 1,
    'quantity' => 3,
    'price' => 500,
]);

// Remove item
Cart::remove([
    'product_id' => 10,
]);

// Get item by index
Cart::item(3);

// Get cart
Cart::get();

// Clear cart
Cart::clear();
```

At the output `Cart::get()` we get the contents of such a structure 

```
[
    "items" => [
        0 => [
            "price" => 500
            "quantity" => 3
            "product_id" => 1
        ]
        ...
    ]
    "total_count" => 1
    "total_price" => 1500
    "total_quantity" => 3
]
```

> If in `config/cart.php` key `merge` set `true`, then after user authorization, cart from `session` will merged with the cart `database`.

# Examples of expanding functionality
For example, create a **helper**-class in the `App\Helpers` namespace and name it `Cart`.

> He must inherit the main **helper**-class — `Helper`.

```php
<?php

namespace App\Helpers;

use WebDevRus\LaravelCart\Helpers\Helper;

class Cart extends Helper
{
    ...
}

```

Don't forget to indicate this in the `config/cart.php`

```php
'session' => [
    ...
    'helper' => \App\Helpers\Cart::class,
],

'database' => [
    ...
    'helper' => \App\Helpers\Cart::class,
],
```

### Example #1
Let's say we need to add a new type of output: instead of the available ones `array` and `object` — `JSON`.

Go to `config/cart.php` and change the key `output`
```php
/**
 * Specify the value json
 */
'output' => 'json',
```
Let's create a method `toJson()` in `App\Helpers\Cart`
```php
public function toJson(array $array): string
{
    return json_encode($array);
}
```
And now `Cart::get()` will return a string on JSON.

### Example #2
In the **helper**-class you can create two methods — `before()` and `after()`.

`before()` — called before the cart is formed .

`after()` — called after the cart has been formed and formatted.

Let's say we need to add a product model to the items in the cart. The `after ()` method is suitable for us, because after `before ()` the model object can be transformed.

In this case, the code will look something like this:
```php
/**
 * @param array|object $cart
 * @return void
 */
public function after(&$cart)
{
    $items = data_get($cart, 'items');
    $model = \App\Models\Product::whereIn('id', Arr::pluck($items, 'product_id'))->get();

    foreach ($items as $i => $item) {
        $product = $model->firstWhere('id', data_get($item, 'product_id'));
        data_set($cart, "items.{$i}.product", $product);
    }
}
```

At the output we get the cart type `array` or `object` with a product model for each position.

# Testing
```console
$ composer test
```