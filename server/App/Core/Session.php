<?php

namespace App\Core;

class Session
{
    static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }
}
