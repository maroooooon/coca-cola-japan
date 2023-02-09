<?php

namespace Coke\Sarp2\Plugin\Adminhtml;

use Magento\Framework\AuthorizationInterface;

class CancelButtonPlugin
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    /**
     * @param \Aheadworks\Sarp2\Block\Adminhtml\Subscription\Edit\CancelButton $subject
     * @param $result
     * @return array|mixed
     */
    public function afterGetButtonData(
        \Aheadworks\Sarp2\Block\Adminhtml\Subscription\Edit\CancelButton $subject,
        $result
    ) {
        if ($this->authorization->isAllowed('Aheadworks_Sarp2::cancel_subscription')) {
            return $result;
        }

        return [];
    }
}
