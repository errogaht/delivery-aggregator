<?php
/**
 * Created by PhpStorm.
 * User: errogaht
 * Date: 19.05.2017
 * Time: 13:26
 */

namespace Errogaht\DeliveryAggregator\Carrier;


use Errogaht\DeliveryAggregator\Carrier\Transfer\DellinTransfer;
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

class Dellin extends AbstractCarrier
{

    const CARRIER_CODE = 'dellin';

    /** @var DellinTransfer */
    protected $carrierTransfer;

    /**
     * @inheritDoc
     */
    public function getCalculateRequestPromise()
    {

        if (!$this->transfer instanceof Transfer) {
            throw new \Exception('Transfer request not found');
        }

        if (!$this->cargo instanceof Cargo) {
            throw new \Exception('Cargo not found');
        }

        if (!$this->client instanceof Client) {
            throw new \Exception('Client not found');
        }

        if (empty($this->config['base_url'])) {
            throw new \InvalidArgumentException('base_url required');
        }

        if (empty($this->config['key'])) {
            throw new \InvalidArgumentException('key required');
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
            'appKey' => $this->config['key'],
            'derivalPoint' => $this->carrierTransfer->from,
            'arrivalPoint' => $this->carrierTransfer->to,
            'derivalDoor' => $this->transfer->isPickup(),
            'arrivalDoor' => $this->transfer->isDelivery(),
            'sizedVolume' => $this->cargo->getVolume()->cubicMetres(),
            'sizedWeight' => $this->cargo->getWeight()->kilograms(),
            'oversizedVolume' => $this->carrierTransfer->cargoOversizeVolume,
            'oversizedWeight' => $this->carrierTransfer->cargoOversizeWeight,
            'length' => $this->cargo->getLength()->metres(),
            'width' => $this->cargo->getWidth()->metres(),
            'height' => $this->cargo->getHeight()->metres(),
            'maxWeight' => $this->cargo->getMaxWeight()->kilograms(),
            'statedValue' => $this->transfer->isInsurance() ? $this->cargo->getCost() : 0,
            'quantity' => 1,
            'packages' => [],
            'derivalServices' => [],
            'arrivalServices' => [],
            /*'derivalLoading' => [
                [
                    'uid' => '0xa77fcf6a449164ed490133777a68bd51'
                ],
                [
                    'uid' => '0xadf1fc002cb8a9954298677b22dbde12',
                    'value' => '4'
                ],
                [
                    'uid' => '0x9a0d647ddb11ebbd4ddaaf3b1d9f7b74',
                    'value' => '58'
                ]
            ],
            'arrivalUnloading' => [
                [
                    'uid' => '0xa77fcf6a449164ed490133777a68bd51'
                ],
                [
                    'uid' => '0xadf1fc002cb8a9954298677b22dbde12',
                    'value' => '4'
                ],
                [
                    'uid' => '0x9a0d647ddb11ebbd4ddaaf3b1d9f7b74',
                    'value' => '58'
                ]
            ],*/
        ];

        //опции груза
        if ($this->carrierTransfer->cargoIsHardPack) {
            $params['packages'][] = '0x838fc70baeb49b564426b45b1d216c15';
        }

        if ($this->carrierTransfer->cargoIsHardBox) {
            $params['packages'][] = '0x8783b183e825d40d4eb5c21ef63fbbfb';
        }

        if ($this->carrierTransfer->cargoIsCardboardBox) {
            $params['packages'][] = '0x951783203a254a05473c43733c20fe72';
        }

        if ($this->carrierTransfer->cargoIsAdditionalPack) {
            $params['packages'][] = '0x9a7f11408f4957d7494570820fcf4549';
        }

        if ($this->carrierTransfer->cargoIsBubbleFilm) {
            $params['packages'][] = '0xa8b42ac5ec921a4d43c0b702c3f1c109';
        }

        if ($this->carrierTransfer->cargoIsBag) {
            $params['packages'][] = '0xad22189d098fb9b84eec0043196370d6';
        }

        if ($this->carrierTransfer->cargoIsPallet) {
            $params['packages'][] = '0xbaa65b894f477a964d70a4d97ec280be';
        }


        //опции забора груза
        if ($this->carrierTransfer->pickupIsOpenCar) {
            $params['derivalServices'][] = '0x9951e0ff97188f6b4b1b153dfde3cfec';
        }

        if ($this->carrierTransfer->pickupIsCarTentOff) {
            $params['derivalServices'][] = '0x818e8ff1eda1abc349318a478659af08';
        }

        if ($this->carrierTransfer->pickupIsManipulator) {
            $params['derivalServices'][] = '0x88f93a2c37f106d94ff9f7ada8efe886';
        }

        if ($this->carrierTransfer->pickupIsTailLift) {
            $params['derivalServices'][] = '0x92fce2284f000b0241dad7c2e88b1655';
        }

        if ($this->carrierTransfer->pickupIsSideLoad) {
            $params['derivalServices'][] = '0xb83b7589658a3851440a853325d1bf69';
        }

        if ($this->carrierTransfer->pickupIsTopLoad) {
            $params['derivalServices'][] = '0xabb9c63c596b08f94c3664c930e77778';
        }


        //опции доставки
        if ($this->carrierTransfer->deliveryIsOpenCar) {
            $params['arrivalServices'][] = '0x9951e0ff97188f6b4b1b153dfde3cfec';
        }

        if ($this->carrierTransfer->deliveryIsCarTentOff) {
            $params['arrivalServices'][] = '0x818e8ff1eda1abc349318a478659af08';
        }

        if ($this->carrierTransfer->deliveryIsManipulator) {
            $params['arrivalServices'][] = '0x88f93a2c37f106d94ff9f7ada8efe886';
        }

        if ($this->carrierTransfer->deliveryIsTailLift) {
            $params['arrivalServices'][] = '0x92fce2284f000b0241dad7c2e88b1655';
        }

        if ($this->carrierTransfer->deliveryIsSideLoad) {
            $params['arrivalServices'][] = '0xb83b7589658a3851440a853325d1bf69';
        }

        if ($this->carrierTransfer->deliveryIsTopLoad) {
            $params['arrivalServices'][] = '0xabb9c63c596b08f94c3664c930e77778';
        }


        return $this->client->requestAsync('POST', "{$this->config['base_url']}/v1/public/calculator.json", [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json;charset=utf-8',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip'
            ],
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
        if (empty($cityName)) {
            throw new \InvalidArgumentException('$cityName is empty');
        }

        $cityName = mb_strtolower(trim($cityName));

        $collection = Collection::make($this->getCarrierCityIdArray());

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
        //$cm->delete('carrier_cities' . $this->code);
        $cities = $cm->get('carrier_cities' . $this->code);
        if (is_null($cities)) {
            $carrierCitiesRawResponse = $this->getCarrierRawCitiesData();
            $cities = Collection::make($carrierCitiesRawResponse['city'])
                ->mapWithKeys(function ($city) {
                    return [$city['code'] => mb_strtolower($city['name'])];
                })->toArray();


            $cm->set('carrier_cities' . $this->code, $cities,
                $this->shippingManager->config['cache']['default_cache_minutes']);
        }
        return $cities;
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
        //$cm->delete('carrier_cities_fat' . $this->code);
        $carrierCitiesRawResponse = $cm->get('carrier_cities_fat' . $this->code);
        if (is_null($carrierCitiesRawResponse)) {

            if (empty($this->config['base_url'])) {
                throw new \InvalidArgumentException('base_url required');
            }

            if (empty($this->config['key'])) {
                throw new \InvalidArgumentException('key required');
            }
            $params = [
                'appKey' => $this->config['key'],
            ];


            $guzzleResp = $this->client->request('POST', "{$this->config['base_url']}/v2/public/terminals.json", [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'Accept' => 'application/json',
                    'Accept-Encoding' => 'gzip'
                ],
                RequestOptions::JSON => $params,
                RequestOptions::VERIFY => false
            ]);


            if ($guzzleResp->getStatusCode() !== 200) {
                throw new \Exception('not 200 OK');
            }
            $body = $guzzleResp->getBody()->getContents();
            if (empty($body)) {
                throw new \Exception('Empty body');
            }
            $carrierCitiesRawResponse = json_decode($body, true);

            if (empty($carrierCitiesRawResponse['city'])) {
                throw new \Exception('dellin  cities list empty');
            }
            $cm->set('carrier_cities_fat' . $this->code, $carrierCitiesRawResponse,
                $this->shippingManager->config['cache']['default_cache_minutes']);
        }
        return $carrierCitiesRawResponse;
    }

    public function getCarrierTransfer()
    {
        if (null === $this->carrierTransfer) {
            $this->carrierTransfer = new DellinTransfer($this);
        }
        return $this->carrierTransfer;
    }

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
        $calculationResponse->setHasError(!empty($result['errors']));

        if ($calculationResponse->isHasError()) {
            $calculationResponse->setErrorData(json_encode($result['errors']));
            return $calculationResponse;
        }

        Collection::make([
            'derival',
            'arrival',
            'price',
            'time',
        ])->each(function ($requiredField) use ($result) {
            if (empty($result[$requiredField])) {
                throw new \Exception("empty $requiredField");
            }
        });

        if (empty($result['derival']['terminals'])) {
            throw new \Exception("empty derival terminals");
        }

        if (empty($result['arrival']['terminals'])) {
            throw new \Exception("empty arrival terminals");
        }

        if (empty($result['time']['value'])) {
            throw new \Exception("empty time value");
        }


        $transferOffers = Collection::make();
        $sourceTerminals = Collection::make($result['derival']['terminals'])->transform(function ($data) {
            Collection::make([
                'id',
                'price',
                'name',
                'address',
            ])->each(function ($requiredField) use ($data) {
                if (!isset($data[$requiredField])) {
                    throw new \Exception("not set $requiredField");
                }
            });

            return (new Terminal())
                ->setAddress($data['address'])
                ->setVid($data['id'])
                ->setUseCost($data['price'])
                ->setIsAcceptanceOnly(false)
                ->setName($data['name']);
        });

        $destinationTerminals = Collection::make($result['arrival']['terminals'])->transform(function ($data) {
            Collection::make([
                'id',
                'price',
                'name',
                'address',
            ])->each(function ($requiredField) use ($data) {
                if (!isset($data[$requiredField])) {
                    throw new \Exception("not set $requiredField");
                }
            });

            return (new Terminal())
                ->setAddress($data['address'])
                ->setVid($data['id'])
                ->setUseCost($data['price'])
                ->setIsAcceptanceOnly(false)
                ->setName($data['name']);
        });

        if (!empty($result['intercity'])) {
            $transferOffer = new TransferOffer();
            $transferOffer->setCarrier($this)
                ->setSourceTerminals($sourceTerminals)
                ->setDestinationTerminals($destinationTerminals)
                ->setRawCarrierResponse($body)
                ->setCarrierCode($this->getCarrierCode())
                ->setType(TransferOffer::TRANSFER_TYPE_GROUND)
                ->setHasError(false)
                ->setCostTotal(new Money($result['price']))
                ->setMaxTerm($result['time']['value'])
                ->setMinTerm($result['time']['value']);

            $transferOffers->push($transferOffer);
        }

        if (!empty($result['air'])) {
            $transferOffer = new TransferOffer();
            $transferOffer->setCarrier($this)
                ->setSourceTerminals($sourceTerminals)
                ->setDestinationTerminals($destinationTerminals)
                ->setRawCarrierResponse($body)
                ->setCarrierCode($this->getCarrierCode())
                ->setType(TransferOffer::TRANSFER_TYPE_AVIA)
                ->setHasError(false)
                ->setCostTotal(new Money(($result['price'] - $result['intercity']['price']) + $result['air']['price']))
                //эти уроды не выдают срок доставки авиа, только цену. чтобы узнать срок нужно. Внимание! скачать эксель таблицу с сайта))
                //->setMaxTerm($result['time']['value'])
                //->setMinTerm($result['time']['value'])
            ;

            $transferOffers->push($transferOffer);
        }


        return $calculationResponse->setOffers($transferOffers);
    }

    public function getCarrierCode()
    {
        return self::CARRIER_CODE;
    }

    /**
     * @inheritDoc
     */
    protected function isPackageOversized()
    {
        return false;
    }


}