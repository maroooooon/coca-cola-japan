<?php

namespace CokeJapan\Hccb\Api\Response;

interface OrderResponseInterface
{
    /**
     * Status api
     *
     * @return array
     */
    public function getItems();

    /**
     * SetItems
     *
     * @param array $items
     * @return mixed
     */
    public function setItems($items);

    /**
     *  String log
     *
     * @return string
     */
    public function toString();
}
