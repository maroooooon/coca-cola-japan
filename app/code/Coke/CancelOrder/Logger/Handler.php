<?php

namespace Coke\CancelOrder\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
    /**
     * @var string
     */
    protected $fileName = '/var/log/coke_cancel_order.log';
}
