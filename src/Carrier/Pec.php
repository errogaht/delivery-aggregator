<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 13:26
 */

namespace Errogaht\DeliveryAggregator\Carrier;

use Errogaht\DeliveryAggregator\Carrier\Pec\TermParser;
use Errogaht\DeliveryAggregator\Carrier\Transfer\PecTransfer;
use Errogaht\DeliveryAggregator\Entity\Cargo;
use Errogaht\DeliveryAggregator\Entity\Terminal;
use Errogaht\DeliveryAggregator\Entity\Transfer;
use Errogaht\DeliveryAggregator\Entity\TransferCalculationResponse;
use Errogaht\DeliveryAggregator\Entity\Unit\Money;
use Errogaht\DeliveryAggregator\Exceptions\CarrierCityNotFoundException;
use Errogaht\DeliveryAggregator\Response\TransferOffer;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class Pec extends AbstractCarrier
{
    const CARRIER_CODE = 'pec';
    /** @var PecTransfer */
    protected $carrierTransfer;

    public function getCarrierTransfer()
    {
        if (null === $this->carrierTransfer) {
            $this->carrierTransfer = new PecTransfer($this);
        }
        return $this->carrierTransfer;
    }

    /**
     * @inheritdoc
     * @throws \Errogaht\DeliveryAggregator\Exceptions\CarrierCityNotFoundException
     */
    public function getCalculateRequestPromise()
    {
        if (!$this->transfer instanceof Transfer) {
            throw new \Exception('Transfer request not found');
        }

        if (!$this->cargo instanceof Cargo) {
            throw new \Exception('Package not found');
        }

        if (!$this->client instanceof Client) {
            throw new \Exception('Client not found');
        }

        if (empty($this->config['base_url'])) {
            throw new \InvalidArgumentException('base_url required');
        }

        if (empty($this->config['login'])) {
            throw new \InvalidArgumentException('login required');
        }

        if (empty($this->config['key'])) {
            throw new \InvalidArgumentException('key required');
        }

        $senderDistanceType = 0;
        if ($this->carrierTransfer->pickupMoscowIsTTK) {
            $senderDistanceType = 3;
        }
        if ($this->carrierTransfer->pickupMoscowIsMOJD) {
            $senderDistanceType = 2;
        }
        if ($this->carrierTransfer->pickupMoscowIsSK) {
            $senderDistanceType = 1;
        }

        $receiverDistanceType = 0;
        if ($this->carrierTransfer->deliveryMoscowIsTTK) {
            $receiverDistanceType = 3;
        }
        if ($this->carrierTransfer->deliveryMoscowIsMOJD) {
            $receiverDistanceType = 2;
        }
        if ($this->carrierTransfer->deliveryMoscowIsSK) {
            $receiverDistanceType = 1;
        }

        if (is_null($this->carrierTransfer->from)) {
            $this->carrierTransfer->from = $this->getCarrierCityId($this->transfer->getCityFrom());
        }

        if (is_null($this->carrierTransfer->to)) {
            $this->carrierTransfer->to = $this->getCarrierCityId($this->transfer->getCityTo());
        }

        if (!is_scalar($this->carrierTransfer->from) || !is_scalar($this->carrierTransfer->to)) {
            throw new \Exception('From / to not configured');
        }

        $params = [
            'senderCityId' => $this->carrierTransfer->from,
            'receiverCityId' => $this->carrierTransfer->to,
            'isOpenCarSender' => $this->carrierTransfer->pickupIsOpenCar,
            'senderDistanceType' => $senderDistanceType,
            'isDayByDay' => $this->carrierTransfer->pickupIsDayByDay,
            'isOpenCarReceiver' => $this->carrierTransfer->deliveryIsOpenCar,
            'receiverDistanceType' => $receiverDistanceType,
            'isHyperMarket' => $this->carrierTransfer->deliveryIsHyperMarket,
            'isInsurance' => $this->transfer->isInsurance(),
            'isInsurancePrice' => $this->cargo->getCost(),
            'isPickUp' => $this->transfer->isPickup(),
            'isDelivery' => $this->transfer->isDelivery(),
            'Cargos' => [
                [
                    'length' => $this->cargo->getLength()->metres(),
                    'width' => $this->cargo->getWidth()->metres(),
                    'height' => $this->cargo->getHeight()->metres(),
                    'volume' => $this->cargo->getVolume()->cubicMetres(),
                    'maxSize' => $this->cargo->getMaxLength()->metres(),
                    'isHP' => $this->carrierTransfer->cargoIsHardPack,
                    'sealingPositionsCount' => $this->carrierTransfer->cargoSealingCount,
                    'weight' => $this->cargo->getWeight()->kilograms(),
                    'overSize' => $this->isPackageOversized()
                ]
            ]
        ];

        return $this->client->requestAsync('POST', "{$this->config['base_url']}/calculator/calculateprice", [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json;charset=utf-8',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
            ],
            RequestOptions::AUTH => [$this->config['login'], $this->config['key']],
            RequestOptions::JSON => $params,
            RequestOptions::VERIFY => false
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getCarrierCityId($cityName)
    {
        $cityName = mb_strtolower(trim($cityName));

        if (empty($cityName)) {
            throw new \InvalidArgumentException('$cityName is empty');
        }

        $citiesListParsed = $this->getCarrierCityIdArray();
        $collection = Collection::make($citiesListParsed);

        if ($id = $collection->search($cityName)) {
            return $id;
        }

        foreach ($collection as $index => $name) {
            if (strpos($name, "$cityName ") === 0) {
                return $index;
            }
        }
        throw new CarrierCityNotFoundException($cityName);
    }

    /**
     * Возвращает массив всех городов что есть у возильщика грузов
     * в простом формате id => $cityName
     *
     * @return array
     * @throws \Exception
     */
    public function getCarrierCityIdArray()
    {
        $cm = $this->shippingManager->getCacheManager();

        $citiesListParsed = $cm->get('carrier_cities' . $this->code);
        //$cm->delete('carrier_cities' . $this->code);
        if (is_null($citiesListParsed)) {
            $response = $this->getCarrierRawCitiesData();
            $cities = Collection::make();
            Collection::make($response['branches'])
                ->each(function ($branch) use ($cities) {
                    Collection::make($branch['cities'])->each(function ($city) use ($cities) {
                        $cities->put($city['bitrixId'], mb_strtolower($city['title']));
                    });
                });
            $citiesListParsed = $cities->toArray();
            $cm->set('carrier_cities' . $this->code, $citiesListParsed,
                $this->shippingManager->config['cache']['default_cache_minutes']);
        }
        return $citiesListParsed;
    }

    /**
     * Возвращает десериализованный массив данных от ответа возильщика
     * по списку городов, какие есть у него
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCarrierRawCitiesData()
    {
        $cm = $this->shippingManager->getCacheManager();

        $response = $cm->get('carrier_cities_fat' . $this->code);
        if (is_null($response)) {

            if (empty($this->config['base_url'])) {
                throw new \InvalidArgumentException('base_url required');
            }

            if (empty($this->config['key'])) {
                throw new \InvalidArgumentException('key required');
            }

            if (empty($this->config['login'])) {
                throw new \InvalidArgumentException('login required');
            }

            $guzzleResp = $this->client->request('GET', "{$this->config['base_url']}/branches/all", [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip',
                ],
                RequestOptions::AUTH => [$this->config['login'], $this->config['key']],
                RequestOptions::VERIFY => false
            ]);


            if ($guzzleResp->getStatusCode() !== 200) {
                throw new \Exception('not 200 OK');
            }
            $body = $guzzleResp->getBody()->getContents();
            if (empty($body)) {
                throw new \Exception('Empty body');
            }
            $response = json_decode($body, true);
            if (empty($response['branches'])) {
                throw new \Exception('pec citiel list empty');
            }

            $cm->set('carrier_cities_fat' . $this->code, $response,
                $this->shippingManager->config['cache']['default_cache_minutes']);
        }
        return $response;
    }

    protected function isPackageOversized()
    {
        if ($this->cargo->getMaxLength()->metres() > 5) {
            return true;
        }
        if ($this->cargo->getWeight()->kilograms() >= 1000) {
            return true;
        }
        return false;
    }

    /**
     * @param ResponseInterface $response
     * @return TransferCalculationResponse
     * @throws \Exception
     */
    public function parseCalculateResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('not 200 OK');
        }
        $body = $response->getBody()->getContents();
        if (empty($body)) {
            throw new \Exception('Empty body');
        }
        $result = json_decode($body, true);

        $calculationResponse = new TransferCalculationResponse();
        $calculationResponse->setHasError($result['hasError']);
        $calculationResponse->setErrorData($result['errorMessage']);

        if ($calculationResponse->isHasError()) {
            return $calculationResponse;
        }
        if (empty($result['commonTerms'][0])) {
            throw new \Exception('empty commonTerms');
        }

        if (empty($result['transfers'])) {
            throw new \Exception('empty transfers');
        }


        if ($this->transfer->isPickup()) {

            if ($this->transfer->isDelivery()) {
                if (empty($result['commonTerms'][0]['transportingWithDeliveryWithPickup'])) {
                    throw new \Exception('empty commonTerms transportingWithDeliveryWithPickup');
                }
                list($termFrom, $termTo) = TermParser::parseMaybeArrayOfTerms($result['commonTerms'][0]['transportingWithDeliveryWithPickup']);
            } else {
                if (empty($result['commonTerms'][0]['transportingWithPickup'])) {
                    throw new \Exception('empty commonTerms transportingWithPickup');
                }
                list($termFrom, $termTo) = TermParser::parseMaybeArrayOfTerms($result['commonTerms'][0]['transportingWithPickup']);
            }
        } else {
            if ($this->transfer->isDelivery()) {
                if (empty($result['commonTerms'][0]['transportingWithDelivery'])) {
                    throw new \Exception('empty commonTerms transportingWithDelivery');
                }
                list($termFrom, $termTo) = TermParser::parseMaybeArrayOfTerms($result['commonTerms'][0]['transportingWithDelivery']);
            } else {
                if (empty($result['commonTerms'][0]['transporting'])) {
                    throw new \Exception('empty commonTerms transporting');
                }
                list($termFrom, $termTo) = TermParser::parseMaybeArrayOfTerms($result['commonTerms'][0]['transporting']);
            }
        }


        $offers = Collection::make($result['transfers'])
            ->map(function ($data) use ($body, $termFrom, $termTo) {
                if ($data['hasError']) {
                    return (new TransferOffer())
                        ->setHasError($data['hasError'])
                        ->setErrorMessage($data['errorMessage']);
                }

                if ($data['transportingType'] === 1) {
                    $type = TransferOffer::TRANSFER_TYPE_GROUND;
                } elseif ($data['transportingType'] === 2) {
                    $type = TransferOffer::TRANSFER_TYPE_AVIA;
                } else {
                    throw new \Exception('undefined transfer type');
                }

                return (new TransferOffer())
                    ->setCarrier($this)
                    ->setDestinationTerminals($this->getTerminalsForCarrierCityId($this->carrierTransfer->to)
                        ->reject(function (Terminal $terminal) {
                            return $terminal->isAcceptanceOnly();
                        }))
                    ->setSourceTerminals($this->getTerminalsForCarrierCityId($this->carrierTransfer->from))
                    ->setRawCarrierResponse($body)
                    ->setCarrierCode($this->getCarrierCode())
                    ->setType($type)
                    ->setHasError($data['hasError'])
                    ->setErrorMessage($data['errorMessage'])
                    ->setCostTotal(new Money($data['costTotal']))
                    ->setMaxTerm($termTo)
                    ->setMinTerm($termFrom);
            })->values();

        return $calculationResponse->setOffers($offers);
    }

    /**
     * Возвращает коллекцию терминалов по указанному ID города
     * ПЭК у нас не выдаёт в ответе рассчёта доставки список терминалов,
     * и тут просто я беру из общего списка всех терминалов те, которыые подходят к указанному городу
     *
     * у ветки могут быть города
     * если есть города то берём этот город и смотрим есть ли у ветки отделения
     * если у ветки есть отделение то выводим как терминал на карту - склад этого отделения
     * выводим все отделения которые есть у этого бранча
     *
     * нужно обратить внимание что у отделения может быть только на приём отправлений а не на выдачу
     *
     * у всех отделений по одному складу
     * @param $cityId
     * @return Collection
     * @throws \Exception
     */
    private function getTerminalsForCarrierCityId($cityId)
    {
        $cityId = (string)$cityId;
        $branches = $this->getCarrierRawCitiesData()['branches'];

        $terminals = Collection::make();
        foreach ($branches as $branch) {
            if (empty($branch['divisions']) || empty($branch['cities'])) {
                continue;
            }

            foreach ($branch['cities'] as $city) {
                if ($cityId === $city['bitrixId']) {
                    foreach ($branch['divisions'] as $division) {
                        foreach ($division['warehouses'] as $warehouse) {
                            $terminal = (new Terminal())
                                ->setAddress($warehouse['address'])
                                ->setVid($warehouse['id'])
                                ->setUseCost(0)
                                ->setName($warehouse['name'])
                                ->setLat(explode(',', $warehouse['coordinates'])[0])
                                ->setLng(explode(',', $warehouse['coordinates'])[1])
                                ->setPhone($warehouse['telephone'])
                                ->setEmail($warehouse['email'])
                                ->setIsAcceptanceOnly($warehouse['isAcceptanceOnly']);
                            $terminals->push($terminal);
                        }
                    }
                    break 2;
                }
            }
        }
        return $terminals;
    }

    public function getCarrierCode()
    {
        return self::CARRIER_CODE;
    }
}