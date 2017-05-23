<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 19:24
 */

namespace Errogaht\DeliveryAggregator\Entity\Unit;


class AbstractUnit implements \JsonSerializable
{
    /** @var int */
    protected $value;

    public function __construct($val)
    {
        $val = (int)$val;
        if ($val === 0) {
            throw new \InvalidArgumentException('Unit cant be 0');
        }
        $this->value = $val;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }
}