<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 19:14
 */

namespace Errogaht\DeliveryAggregator\Entity\Unit;


class Volume extends AbstractUnit
{

    /**
     * @return float
     */
    public function cubicMetres()
    {
        return (float)($this->value / 1000000000);
    }

    /**
     * @return int
     */
    public function cubicMillimetres()
    {
        return $this->value;
    }
}