<?php

namespace CokeJapan\Hccb\Model\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class Handler This handler allows setting log file name via constructor parameter
 *
 */
class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/hccb_api.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
