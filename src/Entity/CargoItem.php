<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 18.05.2017
 * Time: 19:11
 */

namespace Errogaht\DeliveryAggregator\Entity;


use Errogaht\DeliveryAggregator\Entity\Unit\Length;
use Errogaht\DeliveryAggregator\Entity\Unit\Money;
use Errogaht\DeliveryAggregator\Entity\Unit\Volume;
use Errogaht\DeliveryAggregator\Entity\Unit\Weight;

class CargoItem
{
    /** @var Length */
    protected $width;

    /** @var Length */
    protected $length;

    /** @var Length */
    protected $height;

    /** @var Volume */
    protected $volume;

    /** @var Weight */
    protected $weight;

    /** @var Money */
    protected $price;

    /**
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $price float руб
     * @return CargoItem
     */
    public function setPrice($price)
    {
        $this->price = new Money($price);
        return $this;
    }


    /**
     * @return Length
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param $width int мм
     * @return CargoItem
     */
    public function setWidth($width)
    {
        $this->width = new Length($width);
        return $this;
    }

    /**
     * @return Length
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param $length int мм
     * @return CargoItem
     */
    public function setLength($length)
    {
        $this->length = new Length($length);
        return $this;
    }

    /**
     * @return Length
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param $height int мм
     * @return CargoItem
     */
    public function setHeight($height)
    {
        $this->height = new Length($height);
        return $this;
    }

    /**
     * @return Volume
     */
    public function getVolume()
    {
        if (null === $this->volume) {
            $this->volume = $this->calculateVolume();
        }
        return $this->volume;
    }

    /**
     * @return Volume
     */
    public function calculateVolume()
    {
        return new Volume($this->height->getValue() * $this->width->getValue() * $this->length->getValue());
    }

    /**
     * @return CargoItem
     */
    public function setVolume($volume)
    {
        $this->volume = new Volume($volume);
        return $this;
    }

    /**
     * @return Weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param $weight int гр.
     * @return CargoItem
     */
    public function setWeight($weight)
    {
        $this->weight = new Weight($weight);
        return $this;
    }


}