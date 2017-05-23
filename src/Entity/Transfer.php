<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 13:42
 */

namespace Errogaht\DeliveryAggregator\Entity;

/**
 * Class TransferRequest
 * @package Errogaht\DeliveryAggregator\Entity
 *
 * Точка забора TakePoint
 * | transferToSendPoint
 * Точка отправки SendPoint
 * Точка доставки ReceivePoint
 * | transferToDeliveryPoint
 * Точка вручения DeliveryPoint
 */
class Transfer
{
    /** @var bool Нужен ли забор груза у отправителя */
    protected $isPickup = false;

    /** @var bool Нужна ли доставка груза до получателя */
    protected $isDelivery = false;

    /** @var bool Нужна ли страховка */
    protected $isInsurance = true;

    /** @var string название города откуда везём */
    protected $city_from;

    /** @var string название города куда везём */
    protected $city_to;

    /**
     * @return string
     */
    public function getCityFrom()
    {
        return $this->city_from;
    }

    /**
     * @param string $city_from
     * @return Transfer
     */
    public function setCityFrom($city_from)
    {
        $this->city_from = $city_from;
        return $this;
    }

    /**
     * @return string
     */
    public function getCityTo()
    {
        return $this->city_to;
    }

    /**
     * @param string $city_to
     * @return Transfer
     */
    public function setCityTo($city_to)
    {
        $this->city_to = $city_to;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInsurance()
    {
        return $this->isInsurance;
    }

    /**
     * @param bool $isInsurance
     * @return Transfer
     */
    public function setIsInsurance($isInsurance)
    {
        $this->isInsurance = $isInsurance;
        return $this;
    }


    /**
     * @return bool
     */
    public function isPickup()
    {
        return $this->isPickup;
    }

    /**
     * @param bool $isPickup
     * @return Transfer
     */
    public function setIsPickup($isPickup)
    {
        $this->isPickup = $isPickup;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDelivery()
    {
        return $this->isDelivery;
    }

    /**
     * @param bool $isDelivery
     * @return Transfer
     */
    public function setIsDelivery($isDelivery)
    {
        $this->isDelivery = $isDelivery;
        return $this;
    }


}