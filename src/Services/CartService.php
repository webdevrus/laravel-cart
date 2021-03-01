<?php

namespace WebDevRus\LaravelCart\Services;

class CartService
{
    public $class;

    /**
     * @var string
     */
    public $driver;

    public function __construct()
    {
        $this->driver = config('cart.driver') ?? auth()->check()
            ? 'database'
            : 'session';

        $this->boot();
    }

    protected function boot()
    {
        $this->getDriverClass();

        $boot = 'boot' . basename(static::class);
        if (method_exists($this, $boot)) {
            $this->{$boot}();
        }
    }

    private function getDriverClass(): void
    {
        $class = config("cart.{$this->driver}.driver");

        self::inspection($class);

        $this->class = new $class($this->driver);
    }

    public static function inspection(?string $class): void
    {
        if (is_null($class)) {
            throw new \Exception('Class can not be empty.');
        }

        if (!class_exists($class)) {
            throw new \Exception(sprintf('Class \%s is not exists.', $class));
        }
    }
}