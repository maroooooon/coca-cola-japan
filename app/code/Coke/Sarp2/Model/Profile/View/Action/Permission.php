<?php
namespace Coke\Sarp2\Model\Profile\View\Action;

use Aheadworks\Sarp2\Api\Data\ScheduledPaymentInfoInterface;
use Aheadworks\Sarp2\Api\ProfileManagementInterface;
use Aheadworks\Sarp2\Engine\Profile\Checker\PaymentToken as PaymentTokenChecker;
use Aheadworks\Sarp2\Model\Profile\View\Action\Permission as Sarp2Permission;
use Coke\Sarp2\Helper\Config;
use Magento\Framework\Exception\LocalizedException;

class Permission
{
    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;
    /**
     * @var PaymentTokenChecker
     */
    private $profileTokenChecker;
    /**
     * @var Sarp2Permission
     */
    private $sarp2Permission;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ProfileManagementInterface $profileManagement
     * @param PaymentTokenChecker $profileTokenChecker
     * @param Sarp2Permission $sarp2Permission
     * @param Config $config
     */
    public function __construct(
        ProfileManagementInterface $profileManagement,
        PaymentTokenChecker $profileTokenChecker,
        Sarp2Permission $sarp2Permission,
        Config $config
    ) {
        $this->profileManagement = $profileManagement;
        $this->profileTokenChecker = $profileTokenChecker;
        $this->sarp2Permission = $sarp2Permission;
        $this->config = $config;
    }

    /**
     * @param $profileId
     * @return bool
     * @throws LocalizedException
     */
    public function isSkipNextPaymentDateActionAvailable($profileId): bool
    {
        $nextPaymentInfo = $this->profileManagement->getNextPaymentInfo($profileId);
        $nextPaymentStatus = $nextPaymentInfo->getPaymentStatus();

        return $this->sarp2Permission->isCancelStatusAvailable($profileId)
            && $this->config->canSkipNextPaymentDate()
            && $nextPaymentStatus != ScheduledPaymentInfoInterface::PAYMENT_STATUS_REATTEMPT
            && $this->profileTokenChecker->check($profileId);
    }
}
