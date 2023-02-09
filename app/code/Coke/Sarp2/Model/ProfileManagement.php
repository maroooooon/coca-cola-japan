<?php

namespace Coke\Sarp2\Model;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Aheadworks\Sarp2\Engine\Payment\PaymentsList;
use Aheadworks\Sarp2\Engine\Payment\Persistence;
use Aheadworks\Sarp2\Engine\PaymentInterface;
use Coke\Sarp2\Api\ProfileManagementInterface;
use Psr\Log\LoggerInterface;

class ProfileManagement implements ProfileManagementInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var PaymentsList
     */
    private $paymentsList;
    /**
     * @var Persistence
     */
    private $paymentPersistence;
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @param LoggerInterface $logger
     * @param PaymentsList $paymentsList
     * @param Persistence $paymentPersistence
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        LoggerInterface $logger,
        PaymentsList $paymentsList,
        Persistence $paymentPersistence,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->logger = $logger;
        $this->paymentsList = $paymentsList;
        $this->paymentPersistence = $paymentPersistence;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @inheritDoc
     */
    public function updatePayments(int $profileId): bool
    {
        $profile = $this->profileRepository->get($profileId);

        if ($profile->getStatus() != \Aheadworks\Sarp2\Model\Profile\Source\Status::ACTIVE) {
            return false;
        }

        /** Recollect totals */
        $this->profileRepository->save($profile);

        $payments = $this->paymentsList->getLastScheduled($profile->getProfileId());
        foreach ($payments as $payment) {
            $payment->setBaseTotalScheduled($profile->getBaseRegularGrandTotal());
            $payment->setTotalScheduled($profile->getRegularGrandTotal());
            switch ($payment->getType()) {
                case PaymentInterface::TYPE_REATTEMPT:
                    $payment->setPaymentPeriod(PaymentInterface::PERIOD_REGULAR);
                    break;
                default:
                    $payment->setPaymentPeriod(PaymentInterface::PERIOD_REGULAR);
                    $payment->setType(PaymentInterface::TYPE_PLANNED);
                    $payment->setPaymentStatus(PaymentInterface::STATUS_PLANNED);
                    break;
            }
        }
        if (count($payments)) {
            $this->paymentPersistence->massSave($payments);
        }

        return true;
    }
}
