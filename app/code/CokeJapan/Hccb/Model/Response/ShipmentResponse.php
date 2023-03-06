<?php

namespace CokeJapan\Hccb\Model\Response;

use CokeJapan\Hccb\Model\Response\Response;
use CokeJapan\Hccb\Api\Response\ShipmentResponseInterface;

class ShipmentResponse extends Response implements ShipmentResponseInterface
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $orderSkipped;

    /**
     * Construct
     *
     * @param bool $success
     * @param string $message
     * @param string $orderSkipped
     */
    public function __construct(
        bool $success,
        string $message,
        string $orderSkipped
    ) {
        $this->message = $message;
        $this->orderSkipped = $orderSkipped;
        parent::__construct($success);
    }

    /**
     * Message response
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message response
     *
     * @param string $message
     * @return void
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * Order skip api
     *
     * @return string
     */
    public function getOrderSkipped()
    {
        return $this->orderSkipped;
    }

    /**
     * Set Order skip api
     *
     * @param string $orderSkip
     * @return void
     */
    public function setOrderSkipped(string $orderSkip)
    {
        $this->orderSkipped = $orderSkip;
    }

    /**
     * Return String
     *
     * @return false|string
     */
    public function toString()
    {
        $response = [
            'success' => $this->getSuccess()
        ];

        if (!empty($this->getOrderSkipped()) && $this->getOrderSkipped() != '') {
            $response = [
                'message' => $this->getMessage(),
                'order_skipped' => $this->getOrderSkipped(),
                'success' => false
            ];
        }

        return json_encode($response);
    }
}
