<?php

declare(strict_types = 1);

namespace App\Domain;

class Salary
{

    const CURRENCY_EUR = 'EUR';
    const DECIMAL_PLACES = 3;

    /** @var int $amount */
    private $amount;

    /** @var string $currency */
    private $currency;

    /** @return self */
    public static function fromDb(string $amount)
    {
        return new self(self::convertToFloat((int) $amount));
    }

    public function __construct(float $amount, string $currency = self::CURRENCY_EUR)
    {
        //var_dump($amount);
        if ($currency !== self::CURRENCY_EUR) {
            throw new \InvalidArgumentException('Unsupported currency ' . $currency . ', only ' . self::CURRENCY_EUR . ' is supported.');
        }
        $this->amount = self::convertToInt($amount);
        $this->currency = $currency;
    }

    public function __get(string $name)
    {
        if (!\property_exists(__CLASS__, $name)) {
            throw new \InvalidArgumentException('Property not found: ' . __CLASS__ . '::$' . $name);
        }
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        return $this->$name;
    }

    /** @return string */
    public function __toString()
    {
        return self::convertToFloat($this->amount) . ' ' . $this->currency;
    }

    /** @return int */
    public function getInternalAmount()
    {
        return $this->amount;
    }

    //--------------------------------------------------------------------------
    /** @return int */
    private static function convertToInt(float $amount)
    {
        return (int) ($amount * pow(10, self::DECIMAL_PLACES));
    }

    /** @return float */
    private static function convertToFloat(int $amount)
    {
        return round($amount / pow(10, self::DECIMAL_PLACES), self::DECIMAL_PLACES);
    }

    /** @return float */
    private function getAmount()
    {
        return self::convertToFloat($this->amount);
    }

}
