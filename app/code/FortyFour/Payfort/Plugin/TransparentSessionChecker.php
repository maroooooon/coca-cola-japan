<?php

declare(strict_types=1);

namespace FortyFour\Payfort\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionStartChecker;

class TransparentSessionChecker
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Prevents session starting.
     *
     * @param SessionStartChecker $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCheck(SessionStartChecker $subject, bool $result): bool
    {
        if ($result === false) {
            return false;
        }

        $pathInfo = $this->request->getPathInfo();
        if (strpos($pathInfo, 'payfortfort/payment/responseOnline') !== false
            || strpos($pathInfo, 'payfortfort/payment/merchantPageResponse') !== false
        ) {
            return false;
        }

        return true;
    }
}
