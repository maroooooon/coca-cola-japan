<?php

namespace CokeJapan\Hccb\Api\Response;

interface ShipmentResponseInterface extends ResponseInterface
{
    /**
     * Message response
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set message response
     *
     * @param string $message
     * @return void
     */
    public function setMessage(string $message);

    /**
     * Order skip api
     *
     * @return string
     */
    public function getOrderSkipped();

    /**
     * Set Order skip api
     *
     * @param string $orderSkip
     * @return void
     */
    public function setOrderSkipped(string $orderSkip);
}
