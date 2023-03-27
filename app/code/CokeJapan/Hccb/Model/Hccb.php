<?php

namespace CokeJapan\Hccb\Model;

use DateTime;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use CokeJapan\Hccb\Model\Hccb\PullShipments;
use CokeJapan\Hccb\Model\Hccb\SendOrder;
use Magento\Framework\App\Request\Http;
use CokeJapan\Hccb\Api\HccbManagementInterface;
use CokeJapan\Hccb\Api\Response\ShipmentResponseInterface;
use CokeJapan\Hccb\Api\Response\ResponseInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Hccb implements HccbManagementInterface
{
    /**
     * field hccb required
     *
     */
    public const FIELDS_REQUIRED = [
            'ShipmentNumber',
            'PartnerPO',
            'OrderDate',
            'OrderNumber',
            'ShipmentLine.LineNumber',
            'ShipmentLine.ItemIdentifier.SupplierSKU',
            'ShipmentLine.ItemIdentifier.PartnerSKU',
            'ShipmentLine.Quantity',
            'ShipmentLine.QuantityUOM',
            'ShipmentLine.Price',
            'ShipmentLine.ShipmentInfo.Qty',
            'ShipmentLine.ShipmentInfo.TrackingNumber',
            'ShipmentLine.ShipmentInfo.CarrierCode',
        ];

    /**
     * Field hccb
     *
     */
    public const ALL_FIELD_HCCB = [
            "DocumentDate" => "date",
            "ShipmentNumber" => "int",
            "PartnerPO" => "string",
            "OrderDate" => "date",
            "OrderNumber" => "string",
            "ExpectedDeliveryDate" => "date",
            "Payment.Method" => "string",
            "ShipToAddress.CompanyName" => "string",
            "ShipToAddress.FirstName" => "string",
            "ShipToAddress.LastName" => "string",
            "ShipToAddress.Address1" => "string",
            "ShipToAddress.Address2" => "string",
            "ShipToAddress.City" => "string",
            "ShipToAddress.Zip" => "string",
            "ShipToAddress.State" => "string",
            "ShipToAddress.Phone" => "string",
            "ShipToAddress.Email" => "string",
            "ShipFromAddress.CompanyName" => "string",
            "ShipFromAddress.FirstName" => "string",
            "ShipFromAddress.Address1" => "string",
            "ShipFromAddress.Address2" => "string",
            "ShipFromAddress.City" => "string",
            "ShipFromAddress.Zip" => "string",
            "ShipFromAddress.State" => "string",
            "ShipmentLine.LineNumber" => "int",
            "ShipmentLine.ItemIdentifier.SupplierSKU" => "string",
            "ShipmentLine.ItemIdentifier.PartnerSKU" =>"string",
            "ShipmentLine.Description" => "string",
            "ShipmentLine.Quantity" => "int",
            "ShipmentLine.QuantityUOM" =>"string",
            "ShipmentLine.Price" => "decimal",
            "ShipmentLine.ShipmentInfo.DateShipped" => "date",
            "ShipmentLine.ShipmentInfo.Qty" => "int",
            "ShipmentLine.ShipmentInfo.TrackingNumber" => "string",
            "ShipmentLine.ShipmentInfo.CarrierCode" => "string",
            "ShipmentLine.ShipmentInfo.ClassCode" => "string",
            "ShipmentLine.ShipmentInfo.Weight" => "decimal",
            "ShipmentLine.ShipmentInfo.Length" => "decimal",
            "ShipmentLine.ShipmentInfo.Width" =>"decimal",
            "ShipmentLine.ShipmentInfo.Height" =>"decimal"
        ];

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var PullShipments
     */
    protected $pullShipments;

    /**
     * @var SendOrder
     */
    protected $sendOrder;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param PullShipments $pullShipments
     * @param SendOrder $sendOrder
     * @param Http $httpRequest
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        PullShipments $pullShipments,
        SendOrder $sendOrder,
        Http $httpRequest,
        TimezoneInterface $timezone
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->pullShipments = $pullShipments;
        $this->sendOrder = $sendOrder;
        $this->httpRequest = $httpRequest;
        $this->timezone = $timezone;
    }

    /**
     * GetOrder
     *
     * @return array
     */
    public function getOrders()
    {
        $request = $this->httpRequest->getParams();
        $timezone = $this->timezone->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
        $nowDate =  new \DateTime('now', new \DateTimeZone($timezone));
        $nowDate->setTimezone(new \DateTimeZone("UTC"));
        $strToTimeNow = strtotime($nowDate->format('Y-m-d H:i:s'));

        if (isset($request['timestamp'])) {
            if (!$this->isValidDate($request['timestamp'])) {
                $this->throwWebApiException('Timestamp is not formatted correctly.', 400);
            }
            $maxDate =  new \DateTime('1 month ago', new \DateTimeZone($timezone));
            $maxDate->setTimezone(new \DateTimeZone("UTC"));
            $strToTimeMaxDate = strtotime($maxDate->format('Y-m-d H:i:s'));

            $newDateTimeStamp = new \DateTime($request['timestamp'], new \DateTimeZone($timezone));
            $newDateTimeStamp->setTimezone(new \DateTimeZone("UTC"));
            $timestamp = $newDateTimeStamp->format("Y-m-d H:i:s");
            $strToTimestamp = strtotime($timestamp);

            if ($strToTimestamp > $strToTimeNow) {
                $this->throwWebApiException('Don\'t input future date.', 400);
            }
            if ($strToTimeMaxDate > $strToTimestamp) {
                $this->throwWebApiException('Timestamp period too long - maximum 1 month ago.', 400);
            }
        } else {
            $dateDefault = new \DateTime('now');
            $timestamp = $dateDefault->setTime(00, 00, 00);
        }
        $ordersData[]['items'] = $this->sendOrder->execute($nowDate, $timestamp);

        return $ordersData;
    }

    /**
     * Validate time
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function isValidDate(string $date, string $format = 'm/d/Y H:i:s'): bool
    {
        $dateObj = DateTime::createFromFormat($format, $date);
        return $dateObj && $dateObj->format($format) == $date;
    }

    /**
     * Create shipment order
     *
     * @return ShipmentResponseInterface | ResponseInterface
     */
    public function createShipments()
    {
        $json = $this->httpRequest->getContent();
        $shipments = $this->validateField($json);
        $shipments = $this->convertShipment($shipments);
        $orderSkipArray = $this->pullShipments->execute($shipments);

        if (count($orderSkipArray) > 0) {
            $res =  new \CokeJapan\Hccb\Model\Response\ShipmentResponse(
                true,
                __('Shipment has been partially created.'),
                json_encode($orderSkipArray)
            );
        } else {
            $res = json_encode(['success'=>true]);
        }

        return $res;
    }

    /**
     * ConvertShipment
     *
     * @param array $shipments
     * @return array
     */
    public function convertShipment($shipments)
    {
        $convertShipments = [];
        foreach ($shipments as $shipment) {
            if (isset($convertShipments[$shipment->{"OrderNumber"}])) {
                $convertShipments = $this->addItemShipment($convertShipments, $shipment);
                continue;
            }

            $convertShipments[$shipment->{"OrderNumber"}]['ShipmentNumber'] = $shipment->{"ShipmentNumber"};
            $convertShipments = $this->addItemShipment($convertShipments, $shipment);
            $convertShipments[$shipment->{"OrderNumber"}]['OrderNumber'] = $shipment->{"OrderNumber"};
            $convertShipments[$shipment->{"OrderNumber"}]['PartnerPO'] = $shipment->{"PartnerPO"};
            $convertShipments[$shipment->{"OrderNumber"}]['OrderDate'] = $shipment->{"OrderDate"};
            $convertShipments[$shipment->{"OrderNumber"}]['ShipmentInfos'] = [
                'CarrierCode' => $shipment->{"ShipmentLine.ShipmentInfo.CarrierCode"},
                'TrackingNumber' => $shipment->{"ShipmentLine.ShipmentInfo.TrackingNumber"},
            ];
        }

        return $convertShipments;
    }

    /**
     * AddItemShipment
     *
     * @param array $convertShipments
     * @param array $shipment
     * @return array
     */
    public function addItemShipment($convertShipments, $shipment)
    {
        $convertShipments[$shipment->{"OrderNumber"}]['ShipmentLines']
        [] = [
            'sku' => $shipment->{"ShipmentLine.ItemIdentifier.SupplierSKU"},
            'ShipmentNumber' => $shipment->{"ShipmentNumber"},
            'ItemIdentifier' => [
                'SupplierSKU' => $shipment->{"ShipmentLine.ItemIdentifier.SupplierSKU"},
                'PartnerSKU' => $shipment->{"ShipmentLine.ItemIdentifier.PartnerSKU"},
            ],
            'Price' => $shipment->{"ShipmentLine.Price"},
            'RetailPrice' => 0,
            'Cost' => 0,
            'MSRP' => 0,
            'Discounts' => [],
            'ShipmentInfos' => [
                'CarrierCode' => $shipment->{"ShipmentLine.ShipmentInfo.CarrierCode"},
                'TrackingNumber' => $shipment->{"ShipmentLine.ShipmentInfo.TrackingNumber"},
                'Qty' => $shipment->{"ShipmentLine.Quantity"}
            ],
            'Taxes' => [],
            'Quantity' => $shipment->{"ShipmentLine.ShipmentInfo.Qty"},
            'QuantityUOM' => $shipment->{"ShipmentLine.QuantityUOM"},
            'LineNumber' => $shipment->{"ShipmentLine.LineNumber"},
            'Weight' => 0,
            'ExtendedAttributes' => [],
        ];

        return $convertShipments;
    }

    /**
     * Check and decode json string
     *
     * @param string $string
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function validateField($string)
    {
        $data = json_decode($string);
        if (!$data) {
            $this->throwWebApiException('invalid json format', 400);
        }

        if (!property_exists($data, 'items')) {
            $this->throwWebApiException('invalid json format', 400);
        }

        if (count($data->items) == 0) {
            $this->throwWebApiException('there are no items to create a shipment', 400);
        }

        foreach ($data->items as $key => $shipment) {
            foreach (self::ALL_FIELD_HCCB as $field => $type) {
                if (!property_exists($shipment, $field) || $shipment->{"$field"} == '') {
                    continue;
                }
                switch ($type) {
                    case 'date':
                        if (!$this->isValidDate($shipment->{"$field"}, $format = 'm/d/Y H:i:s')) {
                            $this->throwWebApiException(
                                $field.' value field isn\'t timestamp type for item '.$key,
                                400
                            );
                        }
                        break;
                    case 'string':
                        if (!is_string($shipment->{"$field"})) {
                            $this->throwWebApiException($field.' value field isn\'t string type for item '.$key, 400);
                        }
                        break;
                    case 'int':
                        if (!is_int($shipment->{"$field"})) {
                            $this->throwWebApiException($field.' value field isn\'t integer type for item '.$key, 400);
                        }
                        break;
                    case 'decimal':
                        if (!is_numeric($shipment->{"$field"})) {
                            $this->throwWebApiException($field.' value field isn\'t numberic type for item '.$key, 400);
                        }
                        break;
                    default:
                }
            }

            foreach (self::FIELDS_REQUIRED as $field) {
                if (!property_exists($shipment, $field)) {
                    $this->throwWebApiException($field.' field is not found for item '.$key, 400);
                }

                if ($shipment->{"$field"} == '') {
                    $this->throwWebApiException($field.' is a required field for item '.$key, 400);
                }
            }
        }

        return $data->items;
    }

    /**
     * Throw Web API exception and add it to log
     *
     * @param string $msg
     * @param numeric $status
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function throwWebApiException($msg, $status)
    {
        throw new \Magento\Framework\Webapi\Exception(__($msg), $status);
    }
}
