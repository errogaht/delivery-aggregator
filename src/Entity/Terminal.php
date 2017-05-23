<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 22.05.2017
 * Time: 12:56
 */

namespace Errogaht\DeliveryAggregator\Entity;


class Terminal extends GeoObject
{
    /** @var bool работает только на получение */
    protected $isAcceptanceOnly;

    /**
     * @return bool
     */
    public function isAcceptanceOnly()
    {
        return $this->isAcceptanceOnly;
    }

    /**
     * @param bool $isAcceptanceOnly
     * @return Terminal
     */
    public function setIsAcceptanceOnly($isAcceptanceOnly)
    {
        $this->isAcceptanceOnly = $isAcceptanceOnly;
        return $this;
    }


}