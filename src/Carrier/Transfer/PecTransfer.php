<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 15:31
 */

namespace Errogaht\DeliveryAggregator\Carrier\Transfer;


class PecTransfer extends CarrierTransferAbstract
{
    /** @var bool Опция забора груза - требуется ли въезд в пределы Садовое Кольцо ? */
    public $pickupMoscowIsSK = false;

    /** @var bool Опция забора груза - требуется ли въезд в пределы Московской окружной железной дороги  ? */
    public $pickupMoscowIsMOJD = false;

    /** @var bool Опция забора груза - требуется ли въезд в пределы ТТК  ? */
    public $pickupMoscowIsTTK = false;

    /** @var bool Опция забора груза - требуется ли растентовка автомобиля или подача открытой машины  ? */
    public $pickupIsOpenCar = false;

    /** @var bool Опция забора груза - день в день? только для МСК / МО ? */
    public $pickupIsDayByDay = false;


    /** @var bool Опция доставки груза - требуется ли растентовка автомобиля или подача открытой машины  ? */
    public $deliveryIsOpenCar = false;

    /** @var bool Опция доставки груза - требуется ли въезд в пределы Садовое Кольцо ? */
    public $deliveryMoscowIsSK = false;

    /** @var bool Опция доставки груза - требуется ли въезд в пределы Московской окружной железной дороги  ? */
    public $deliveryMoscowIsMOJD = false;

    /** @var bool Опция доставки груза - требуется ли въезд в пределы ТТК  ? */
    public $deliveryMoscowIsTTK = false;

    /** @var bool Опция доставки груза - нужно ли доставить в гипермаркет? */
    public $deliveryIsHyperMarket = false;


    /** @var bool Опция груза - нужна ли жёсткая упаковка? */
    public $cargoIsHardPack = false;

    /** @var int Опция груза - количество мест для опломбировки */
    public $cargoSealingCount = 0;


}