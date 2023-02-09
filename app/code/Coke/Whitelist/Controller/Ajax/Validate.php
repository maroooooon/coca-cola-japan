<?php

namespace Coke\Whitelist\Controller\Ajax;

use Coke\Whitelist\Exception\WhitelistEntityContainsIllegalCharacterException;
use Coke\Whitelist\Exception\WhitelistEntityDeniedException;
use Coke\Whitelist\Exception\WhitelistEntityNotFoundException;
use Coke\Whitelist\Model\WhiteListHelper;
use Exception;
use Coke\Whitelist\Model\ModuleConfig;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Validate
 *
 * @package \Coke\NameManagement\Controller\Ajax
 */
class Validate extends Action
{

    /**
     * @var Http
     */
    private $http;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var ModuleConfig
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var WhiteListHelper
     */
    protected $whiteListHelper;
    /**
     * @var WhitelistRepositoryInterface
     */
    protected $whitelistRepository;
    /**
     * @var WhitelistTypeRepositoryInterface
     */
    protected $whitelistTypeRepository;
    /**
     * @var ProductCustomOptionRepositoryInterface
     */
    private $customOptionRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Validate constructor.
     * @param Http $http
     * @param Json $json
     * @param Context $context
     * @param ModuleConfig $config
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param WhiteListHelper $whiteListHelper
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param ProductCustomOptionRepositoryInterface $customOptionRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Http $http,
        Json $json,
        Context $context,
        ModuleConfig $config,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        WhiteListHelper $whiteListHelper,
        WhitelistRepositoryInterface $whitelistRepository,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        ProductCustomOptionRepositoryInterface $customOptionRepository,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->http = $http;
        $this->json = $json;
        $this->context = $context;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->whiteListHelper = $whiteListHelper;
        $this->whitelistRepository = $whitelistRepository;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->customOptionRepository = $customOptionRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $value = $this->getRequest()->getParam('value');
            $typeId = $this->getRequest()->getParam('typeId');
            $validatedValue = $this->validatePhrase($value, $typeId);
            return $resultJson->setData(['status' => 'success', 'validatedValue' => $validatedValue]);
        } catch (WhitelistEntityNotFoundException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'not_found', 'error' => $e->getMessage()]);
        } catch (WhitelistEntityDeniedException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'denied', 'error' => $e->getMessage()]);
        } catch (WhitelistEntityContainsIllegalCharacterException $e) {
            $resultJson->setHttpResponseCode(400);
            return $resultJson->setData(['status' => 'illegal_character', 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $resultJson->setHttpResponseCode(400);
            $this->logger->error($e->getMessage());
            return $resultJson->setData(['status' => 'error', 'error' => 'Unknown error. Please contact customer service.']);
        }
    }

    /**
     * Check if phrase is valid
     *
     * @param $phrase
     * @return string
     */
    protected function validatePhrase($value, $typeId)
    {
            $storeId = $this->storeManager->getStore()->getId();

            $this->whitelistRepository->isDeniedValue($value, $storeId, $typeId);

            $approvedValue = $this->whitelistRepository->isValueSortaApproved($value, $storeId, $typeId);

            $this->whitelistRepository->containsIllegalCharacter($approvedValue, $typeId);

            return $approvedValue;
    }

    /**
     * Create json response
     *
     * @param string $response
     * @return HttpInterface
     */
    public function jsonResponse($response = ''): HttpInterface
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->json->serialize($response)
        );
    }
}
