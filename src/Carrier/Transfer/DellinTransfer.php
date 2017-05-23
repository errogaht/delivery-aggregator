<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 15:31
 */

namespace Errogaht\DeliveryAggregator\Carrier\Transfer;


class DellinTransfer extends CarrierTransferAbstract
{
    /** @var bool Опция забора груза - требуется ли "Боковая загрузка" ? */
    public $pickupIsSideLoad = false;

    /** @var bool Опция забора груза - требуется ли "Верхняя загрузка" ? */
    public $pickupIsTopLoad = false;

    /** @var bool Опция забора груза - требуется ли "Гидроборт" ? */
    public $pickupIsTailLift = false;

    /** @var bool Опция забора груза - требуется ли "Манипулятор" ? */
    public $pickupIsManipulator = false;

    /** @var bool Опция забора груза - требуется ли "Открытая машина" ? */
    public $pickupIsOpenCar = false;

    /** @var bool Опция забора груза - требуется ли "Растентовка" ? */
    public $pickupIsCarTentOff = false;


    /** @var bool Опция доставки груза - требуется ли "Боковая загрузка" ? */
    public $deliveryIsSideLoad = false;

    /** @var bool Опция доставки груза - требуется ли "Верхняя загрузка" ? */
    public $deliveryIsTopLoad = false;

    /** @var bool Опция доставки груза - требуется ли "Гидроборт" ? */
    public $deliveryIsTailLift = false;

    /** @var bool Опция доставки груза - требуется ли "Манипулятор" ? */
    public $deliveryIsManipulator = false;

    /** @var bool Опция доставки груза - требуется ли "Открытая машина" ? */
    public $deliveryIsOpenCar = false;

    /** @var bool Опция доставки груза - требуется ли "Растентовка" ? */
    public $deliveryIsCarTentOff = false;


    /** @var bool Опция груза - объём негабаритной части груза в метрах кубических (необязательный параметр) */
    public $cargoOversizeVolume = 0;

    /** @var bool Опция груза - вес негабаритной части груза в килограммах (необязательный параметр) */
    public $cargoOversizeWeight = 0;

    /** @var bool Опция груза - нужна ли жесткая упаковка ? */
    public $cargoIsHardPack = false;

    /** @var bool Опция груза - нужна ли упаковка "жесткий короб" ? */
    public $cargoIsHardBox = false;

    /** @var bool Опция груза - нужна ли упаковка "картонная коробка" ? */
    public $cargoIsCardboardBox = false;

    /** @var bool Опция груза - нужна ли упаковка "дополнительная упаковка" ? */
    public $cargoIsAdditionalPack = false;

    /** @var bool Опция груза - нужна ли упаковка "воздушно-пузырьковая плёнка" ? */
    public $cargoIsBubbleFilm = false;

    /** @var bool Опция груза - нужна ли упаковка "мешок" ? */
    public $cargoIsBag = false;

    /** @var bool Опция груза - нужна ли упаковка "паллетный борт (только до терминала-получателя)" ? */
    public $cargoIsPallet = false;


}