<?php

namespace CokeJapan\Hccb\Api\Response;

interface ResponseInterface
{
    /**
     * Status api
     *
     * @return bool
     */
    public function getSuccess();

    /**
     * Set status api
     *
     * @param bool $success
     * @return void
     */
    public function setSuccess(bool $success);

    /**
     *  String log
     *
     * @return string
     */
    public function toString();
}
