<?php

declare(strict_types = 1);

namespace App;

use App\Db;

/**
 * Escape Utilities
 * 
 * @see https://phpfashion.com/escapovani-definitivni-prirucka
 */
class Escape
{

    const CONTEXT_HTML = 'HTML';
    const CONTEXT_URL = 'URL';
    const CONTEXT_XML = 'XML';

    /** @internal only keys are relevant */
    private static $validContexts = [
        self::CONTEXT_HTML => 1,
        self::CONTEXT_URL => 1,
        self::CONTEXT_XML => 1,
    ];

    public static function escapeArray(array $array, string $context = self::CONTEXT_HTML)
    {
        if (!self::isValid($context)) {
            throw new \InvalidArgumentException('Invalid context ' . $context);
        }
        $method = 'escape' . $context;
        \array_walk($array, function (&$v, $k, $method) {
            $v = self::$method($v);
        }, $method);
        return $array;
    }

    public static function escapeValue($value, string $context = self::CONTEXT_HTML)
    {
        if (!self::isValid($context)) {
            throw new \InvalidArgumentException('Invalid context ' . $context);
        }
        $method = 'escape' . $context;
        return self::$method($value);
    }

    public static function escapeHTML($s)
    {
        return \htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }

    public static function escapeURL($s)
    {
        return \rawurlencode((string) $s);
    }

    public static function escapeXML($s)
    {
        return \htmlspecialchars(\preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '', (string) $s), ENT_QUOTES, 'UTF-8');
    }

//------------------------------------------------------------------------------  
    private static function isValid(string $context)
    {
        return isset(self::$validContexts[$context]);
    }

}
