# Laravel E-Commerce / Cart

<p align="center"><img src="https://github.com/webdevrus/webdevrus/blob/master/assets/laravel-cart.png?raw=true" alt="Laravel E-Commerce / Cart"></p>

## Русский | [English](README.md)

| Cart | Laravel |
| ---- | ------- |
| 1.x  | 7.x     |

## Установка
```console
$ composer require webdevrus/laravel-cart
```

1. Миграции
```console
$ php artisan vendor:publish --provider="WebDevRus\LaravelCart\Providers\CartServiceProvider" --tag="migrations"
```
```console
$ php artisan migrate
```
2. Конфигурация
```console
$ php artisan vendor:publish --provider="WebDevRus\LaravelCart\Providers\CartServiceProvider" --tag="config"
```
3. Указать трейт `HasCart` в модели `User`.
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
## Использование
```php
use WebDevRus\LaravelCart\Facades\Cart;

...

// Добавить товар в корзину
Cart::add([
    'product_id' => 10,
    'quantity' => 3,
    'price' => 500,
]);

// Уменьшить количество товара в корзине
Cart::remove([
    'product_id' => 10,
    'quantity' => -1,
]);

// Удалить товар из корзины
Cart::remove([
    'product_id' => 10,
]);

// Вывод товара по позиции
Cart::item(3);

// Вывод корзины
Cart::get();

// Очистить корзину
Cart::clear();
```

На выходе `Cart::get()` получаем содержимое такой структуры

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

> Если в `config/cart.php` ключ `merge` в значении `true`, то после авторизации пользователя, корзина из `session` сольётся с корзиной `database`.

# Пример дополнения функционала
Для примера создадим **helper**-класс в пространстве `App\Helpers` и назовём его `Cart`.

> Он должен наследовать основной **helper**-класс расширения — `Helper`.

```php
<?php

namespace App\Helpers;

use WebDevRus\LaravelCart\Helpers\Helper;

class Cart extends Helper
{
    ...
}

```

Не забываем указать это в `config/cart.php`

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

### Пример #1
Допустим, нам нужно добавить новый тип вывода: вместо доступных `array` и `object` — `JSON`.

Перейдём в `config/cart.php` и изменим ключ `output`
```php
/**
 * Указываем значение json
 */
'output' => 'json',
```
Создадим в `App\Helpers\Cart` метод `toJson()`
```php
public function toJson(array $array): string
{
    return json_encode($array);
}
```
Теперь `Cart::get()` будет отдавать строку в виде JSON.

### Пример #2
В **helper**-классе можно создать два метода — `before()` и `after()`.

`before()` — вызывается до формирования корзины.

`after()` — вызывается после формирования и форматирования корзины.

Допустим, нам необходимо добавить к элементам в корзине модель товара. Нам подойдёт метод `after()`, т.к. после `before()` объект модели может преобразоваться.

В таком случае, код будет выглядеть примерно так:
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
На выходе мы получим корзину типа `array` или `object` с моделью товара для каждой позиции.

# Тестирование
```console
$ composer test
```