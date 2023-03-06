<?php

namespace CokeJapan\Hccb\Model\Response;

use CokeJapan\Hccb\Api\Response\OrderResponseInterface;

class OrderResponse implements OrderResponseInterface
{
    /**
     * Message
     *
     * @var array
     */
    protected $items;

    /**
     * @param array $items
     */
    public function __construct(
        $items
    ) {
        $this->items = $items;
    }

    /**
     * Status api
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * SetItems
     *
     * @param array $items
     * @return mixed|void
     */
    public function setItems($items)
    {
        $this->items = $items;
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
