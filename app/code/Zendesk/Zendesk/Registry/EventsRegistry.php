<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2020 Classy Llama
 */


namespace Zendesk\Zendesk\Registry;

/**
 * Class EventsRegistry
 * @package Zendesk\Zendesk\Registry
 */
class EventsRegistry
{
    /**
     * @var
     */
    private $eventData;

    /**
     * @param $event
     * Set the eventData to the event that is passed in.
     */
    public function set($event)
    {
        $this->eventData = $event;
    }

    /**
     * @return bool|mixed
     * return the event data if it exists, othewise just return false
     */
    public function get()
    {
        return $this->eventData ?? false;
    }
}
