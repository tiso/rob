<?php

declare(strict_types = 1);

namespace App\Domain;

abstract class Entity
{

    /** Force child class to define constant */
    const TYPE = self::TYPE;
    const ABSTRACT_NAME = 'Entity';

    private $instanceClassName;

    public function __construct()
    {
        $this->instanceClassName = \str_replace(self::ABSTRACT_NAME, static::TYPE, __CLASS__);
    }

    public function __get(string $name)
    {
        if (!\property_exists($this->instanceClassName, $name)) {
            throw new \InvalidArgumentException('Property not found: ' . $this->instanceClassName . '::$' . $name);
        }
        return $this->$name;
    }

}
