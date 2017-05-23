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
use Errogaht\DeliveryAggregator\LAFFPack;
use Illuminate\Support\Collection;

class Cargo
{
    protected $items = [];

    /** @var Weight */
    protected $weight;

    /** @var Length */
    protected $width;

    /** @var Length */
    protected $length;

    /** @var Length */
    protected $height;

    /** @var Volume */
    protected $volume;

    /** @var Weight */
    protected $maxWeight;


    /** @var Length максимальная длинна из ширины высоты и длинны груза */
    protected $maxLength;

    /** @var Money */
    protected $cost;

    public function addItem(CargoItem $item, $qty = 1)
    {
        $this->validateitem($item);
        for ($i = 0; $i < $qty; $i++) {
            $this->items[] = $item;
        }
    }

    /**
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * @param CargoItem $item
     * @throws \InvalidArgumentException
     */
    private function validateitem(CargoItem $item)
    {
        if (is_null($item->getWeight())) {
            throw new \InvalidArgumentException('cargo item attribute must be set weight');
        }

        if (is_null($item->getPrice())) {
            throw new \InvalidArgumentException('cargo item attribute must be set price');
        }

        if (is_null($item->getHeight())) {
            throw new \InvalidArgumentException('cargo item attribute must be set height');
        }

        if (is_null($item->getWidth())) {
            throw new \InvalidArgumentException('cargo item attribute must be set width');
        }

        if (is_null($item->getLength())) {
            throw new \InvalidArgumentException('cargo item attribute must be set length');
        }

    }

    /**
     * @return Weight
     */
    public function getWeight()
    {
        if (null === $this->weight) {
            $this->calculateValues();
        }
        return $this->weight;
    }

    /**
     * @return Length
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    public function calculateValues()
    {
        if (empty($this->items)) {
            throw new \Exception('Items array is empty. Can\'t calculate dimensions');
        }


        $weight = $volume = $h = $w = $l = $maxWeight = $price = $maxLength = 0;
        /** @var CargoItem $item */
        foreach ($this->items as $item) {
            $weight += $item->getWeight()->getValue();
            $price += $item->getPrice()->getValue();
            $maxWeight = $item->getWeight()->getValue() > $maxWeight ? $item->getWeight()->getValue() : $maxWeight;
            $h = $item->getHeight()->getValue() > $h ? $item->getHeight()->getValue() : $h;
            $w = $item->getWidth()->getValue() > $w ? $item->getWidth()->getValue() : $w;
            $l = $item->getLength()->getValue() > $l ? $item->getLength()->getValue() : $l;

            foreach ([$h, $w, $l] as $i) {
                $maxLength = $i > $maxLength ? $i : $maxLength;
            }
        }

        $this->weight = new Weight($weight);
        $this->maxWeight = new Weight($maxWeight);
        $this->maxLength = new Length($maxLength);

        $this->cost = new Money($price);

        $boxes = Collection::make($this->items)->transform(function (CargoItem $item) {
            return [
                'length' => $item->getLength()->getValue(),
                'width' => $item->getWidth()->getValue(),
                'height' => $item->getHeight()->getValue()
            ];
        })->toArray();
        $laff = new LAFFPack();
        $laff->pack($boxes);
        $dim = $laff->get_container_dimensions();
        $this->width = new Length($dim['width']);
        $this->length = new Length($dim['length']);
        $this->height = new Length($dim['height']);

        $this->volume = new Volume($this->width->getValue() * $this->length->getValue() * $this->height->getValue());
    }

    /**
     * @return Money
     */
    public function getCost()
    {
        if (null === $this->cost) {
            $this->calculateValues();
        }
        return $this->cost;
    }

    /**
     * @return Weight
     */
    public function getMaxWeight()
    {
        if (null === $this->maxWeight) {
            $this->calculateValues();
        }
        return $this->maxWeight;
    }

    /**
     * @return Length
     */
    public function getWidth()
    {
        if (null === $this->width) {
            $this->calculateValues();
        }
        return $this->width;
    }

    /**
     * @return Length
     */
    public function getLength()
    {
        if (null === $this->length) {
            $this->calculateValues();
        }
        return $this->length;
    }

    /**
     * @return Length
     */
    public function getHeight()
    {
        if (null === $this->height) {
            $this->calculateValues();
        }
        return $this->height;
    }

    /**
     * @return Volume
     */
    public function getVolume()
    {
        if (null === $this->volume) {
            $this->calculateValues();
        }
        return $this->volume;
    }


}