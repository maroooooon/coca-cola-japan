<?php

namespace Coke\Sarp2\Service;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\ProfileManagementInterface;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Aheadworks\Sarp2\Model\Profile\Source\Status;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Subscription
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param LoggerInterface $logger
     * @param ProfileManagementInterface $profileManagement
     * @param ProfileRepositoryInterface $profileRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        LoggerInterface $logger,
        ProfileManagementInterface $profileManagement,
        ProfileRepositoryInterface $profileRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->profileManagement = $profileManagement;
        $this->profileRepository = $profileRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param CustomerInterface $customer
     */
    public function cancelAllCustomerSubscriptions(CustomerInterface $customer)
    {
        if ($profiles = $this->getProfiles($customer)) {
            foreach ($profiles as $profile) {
                $this->cancelSubscription($profile);
            }
        }
    }

    /**
     * @param ProfileInterface $profile
     * @return void
     */
    public function cancelSubscription(ProfileInterface $profile)
    {
        try {
            $this->profileManagement->changeStatusAction($profile->getProfileId(), Status::CANCELLED);
            $this->logger->info(
                __(
                    '[Subscriptions::cancelCustomerSubscriptions] Canceled profile: %1.',
                    $profile->getIncrementId()
                )
            );
        } catch (LocalizedException $e) {
            $this->logger->info(
                __(
                    '[Subscriptions::cancelCustomerSubscriptions] Unable to cancel profile: %1. Error: %2',
                    $profile->getIncrementId(),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param CustomerInterface $customer
     * @return ProfileInterface[]|null
     */
    private function getProfiles(CustomerInterface $customer): ?array
    {
        try {
            return $this->profileRepository->getList($this->buildSearchCriteria($customer))->getItems();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param CustomerInterface $customer
     * @return SearchCriteria
     */
    private function buildSearchCriteria(CustomerInterface $customer): SearchCriteria
    {
        return $this->searchCriteriaBuilder->addFilter(ProfileInterface::CUSTOMER_ID, $customer->getId())
            ->addFilter(ProfileInterface::STATUS, [Status::CANCELLED, Status::EXPIRED], 'nin')
            ->create();
    }
}
