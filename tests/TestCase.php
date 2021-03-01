<?php

namespace WebDevRus\LaravelCart\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setDriver();
    }

    protected function setDriver(): void
    {
        if (method_exists($this, 'auth')) {
            call_user_func([$this, 'auth']);
        }
    }
}
