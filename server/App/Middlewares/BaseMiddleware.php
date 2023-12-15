<?php

declare(strict_types = 1);

namespace App\Middlewares;

abstract class BaseMiddleware
{
    abstract public function process();
}
