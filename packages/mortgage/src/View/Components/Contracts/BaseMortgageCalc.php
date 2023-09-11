<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\Contracts;

use Exception;

/**
 * @method void setMinPrice(int|float $value)
 * @method int|float getMinPrice()
 *
 * @method void setMeanPrice(null|int|float $value)
 *
 * @method void setMaxPrice(int|float $value)
 * @method int|float getMaxPrice()
 *
 * @method void setMinFirstPaymentPercent(float $value)
 * @method float getMinFirstPaymentPercent()
 *
 * @method void setMeanFirstPaymentPercent(null|float $value)
 *
 * @method void setMaxFirstPaymentPercent(float $value)
 * @method float getMaxFirstPaymentPercent()
 *
 * @method void setMinTime(int $value)
 * @method int getMinTime()
 *
 * @method void setMeanTime(null|int $value)
 *
 * @method void setMaxTime(int $value)
 * @method int getMaxTime()
 */
abstract class BaseMortgageCalc
{
    public const MIN_VALUE = 0;
    public const MIN_FLOAT_VALUE = 0.0;

    public const MIN_PRICE = self::MIN_VALUE;
    public const MAX_PRICE = self::MIN_VALUE;

    public const MIN_FIRST_PAYMENT_PERCENT = self::MIN_FLOAT_VALUE;
    public const MAX_FIRST_PAYMENT_PERCENT = 100.0;

    public const MIN_TIME = 1;
    public const MAX_TIME = 50;

    protected const PERCENT = 0.01;

    protected int|float $minPrice = self::MIN_VALUE;
    protected null|int|float $meanPrice = null;
    protected int|float $maxPrice = self::MIN_VALUE;

    protected float $minFirstPaymentPercent = self::MIN_FIRST_PAYMENT_PERCENT;
    protected null|float $meanFirstPaymentPercent = null;
    protected float $maxFirstPaymentPercent = self::MAX_FIRST_PAYMENT_PERCENT;

    protected int $minTime = self::MIN_VALUE;
    protected null|int $meanTime = null;
    protected int $maxTime = self::MIN_VALUE;

    /**
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        $action = strtolower(substr($name, 0, 3));
        $propertyName = lcfirst(substr($name, 3, mb_strlen($name)));

        if (!in_array($action, ['set', 'get']) || !property_exists($this, $propertyName)) {
            throw new Exception('Method `' . $name . '` not exists');
        }

        if ($action === 'get') {
            return $this->{$propertyName};
        }

        $curValue = &$this->{$propertyName};
        $newValue = array_shift($arguments);

        $isMin = stripos($propertyName, 'min') !== false;
        $method = $isMin ? 'min' : 'max';
        $edgeValue = $this->getEdgeValues()[$propertyName] ?? null;

        if ($edgeValue !== null) {
            $newValue = call_user_func($isMin ? 'max' : 'min', $edgeValue, $newValue);
        }

        if (!$curValue) {
            $curValue = $newValue;
        }

        $curValue = call_user_func($method, $curValue ?? $newValue, $newValue);

        return null;
    }

    public function getMeanPrice(): int|float
    {
        return $this->meanPrice ?? $this->getMean($this->minPrice, $this->maxPrice);
    }

    public function getMeanFirstPaymentPercent(): float
    {
        return $this->meanFirstPaymentPercent ?? $this->getMean(
            $this->minFirstPaymentPercent,
            $this->maxFirstPaymentPercent
        );
    }

    public function getMinFirstPayment(): int|float
    {
        return round($this->getMinPrice() * $this->getMinFirstPaymentPercent() * static::PERCENT);
    }

    public function getMeanFirstPayment(): int|float
    {
        return round($this->getMeanPrice() * $this->getMeanFirstPaymentPercent() * static::PERCENT);
    }

    public function getMaxFirstPayment(): int|float
    {
        return round($this->getMaxPrice() * $this->getMaxFirstPaymentPercent() * static::PERCENT);
    }

    public function getMeanTime(): int|float
    {
        return $this->meanTime ?? $this->getMean($this->minTime, $this->maxTime);
    }

    protected function getMean(int|float $a, int|float $b): int|float
    {
        return round(($a + $b) / 2);
    }

    protected function getEdgeValues(): array
    {
        return [
            'minTime' => static::MIN_TIME,

            'meanFirstPaymentPercent' => static::MAX_FIRST_PAYMENT_PERCENT,
            'meanTime' => static::MAX_TIME,

            'maxFirstPaymentPercent' => static::MAX_FIRST_PAYMENT_PERCENT,
            'maxTime' => static::MAX_TIME
        ];
    }

    public function getMinAmount(): int|float
    {
        return max($this->getMinPrice() - $this->getMaxFirstPayment(), static::MIN_VALUE);
    }

    public function getAmount(): int|float
    {
        return max($this->getMinPrice(), $this->getMeanPrice()) - $this->getMeanFirstPayment();
    }

    public function getMaxAmount(): int|float
    {
        return $this->getMaxPrice() - $this->getMinFirstPayment();
    }
}
