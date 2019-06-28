<?php

declare(strict_types = 1);

namespace App;

/**
 * Extendable Singleton
 * @see https://blog.cotten.io/how-to-screw-up-singletons-in-php-3e8c83b63189
 */
abstract class Singleton
{

    private static $instances = [];

    protected function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    private function __wakeup()
    {
        
    }

    /** @return mixed */
    public static function getInstance(string $sufix = '')
    {
        $key = get_called_class();
        if (!empty($sufix)) {
            $key .= '.' . $sufix;
        }
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new static();
        }
        return self::$instances[$key];
    }

}
