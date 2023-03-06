<?php

namespace CokeJapan\Hccb\Model\Response;

use CokeJapan\Hccb\Api\Response\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * Message
     *
     * @var bool
     */
    protected $success;

    /**
     * @param bool $success
     */
    public function __construct(
        bool $success
    ) {
        $this->success = $success;
    }

    /**
     * Status api
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set status api
     *
     * @param bool $success
     * @return void
     */
    public function setSuccess(bool $success)
    {
        $this->success = $success;
    }

    /**
     * Return String
     *
     * @return false|string
     */
    public function toString()
    {
        return json_encode($this);
    }
}
