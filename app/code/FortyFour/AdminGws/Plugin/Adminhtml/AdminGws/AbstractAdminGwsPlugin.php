<?php

namespace FortyFour\AdminGws\Plugin\Adminhtml\AdminGws;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\User\Model\User;
use Psr\Log\LoggerInterface;

abstract class AbstractAdminGwsPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Session
     */
    protected $authSession;
    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param LoggerInterface $logger
     * @param Session $authSession
     * @param StoreRepositoryInterface $storeRepository
     * @param RequestInterface $request
     */
    public function __construct(
        LoggerInterface $logger,
        Session $authSession,
        StoreRepositoryInterface $storeRepository,
        RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->authSession = $authSession;
        $this->storeRepository = $storeRepository;
        $this->request = $request;
    }

    /**
     * @return User|null
     */
    protected function getAdminUser(): ?User
    {
        return $this->authSession->getUser();
    }

    /**
     * @return StoreInterface[]
     */
    protected function getStores()
    {
        $stores = $this->storeRepository->getList();

        if (!$this->getAdminUser()->getRole()->getGwsIsAll()) {
            $storeIds = $this->getAdminUser()->getRole()->getGwsStores();

            foreach ($stores as $key => $store) {
                if ($store->getId() != 0 && !in_array($store->getId(), $storeIds)) {
                    unset($stores[$key]);
                }
            }
        }
        return $stores;
    }
}
