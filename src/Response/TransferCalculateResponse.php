<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 17:41
 */

namespace Errogaht\DeliveryAggregator\Response;


class TransferCalculateResponse
{
    /** @var bool */
    protected $hasError = false;

    /** @var string */
    protected $errorMessage;

    /** @var array */
    protected $offers = [];
}