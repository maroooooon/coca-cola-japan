<?php

namespace Coke\Cron\Plugin\Model;

use Magento\Framework\MessageQueue\Consumer\ConfigInterface;

class SchedulePlugin
{
    /**
     * Consumer config provider
     *
     * @var ConfigInterface
     */
    private $consumerConfig;

    /**
     * @param ConfigInterface $consumerConfig
     */
    public function __construct(ConfigInterface $consumerConfig)
    {
        $this->consumerConfig = $consumerConfig;
    }

    /**
     * Get an individual configuration for a job_code
     *
     * @return array|false
     */
    public function afterGetJobConfig(\MageMojo\Cron\Model\Schedule $subject, $jobconfig)
    {
        if ($jobconfig && isset($jobconfig["consumers"]) && $jobconfig["consumers"]) {
            $consumers    = $this->consumerConfig->getConsumers();
            $consumerName = str_replace("mm_consumer_", "", (string)$jobconfig["name"]);
            if (!isset($consumers[$consumerName])) {
                return false;
            }
        }

        return $jobconfig;
    }
}