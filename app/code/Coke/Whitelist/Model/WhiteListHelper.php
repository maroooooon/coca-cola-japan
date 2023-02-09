<?php


namespace Coke\Whitelist\Model;


use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Coke\Whitelist\Exception\WhitelistEntityMaxLengthException;
use Coke\Whitelist\Model\Source\Status as WhitelistStatus;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Coke\Whitelist\Model\ModuleConfig;
use Coke\Whitelist\Model\ResourceModel\Whitelist\CollectionFactory;
use Psr\Log\LoggerInterface;

class WhiteListHelper
{
    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Coke\Whitelist\Model\ModuleConfig
     */
    private $config;

    /**
     * @var CollectionFactory
     */
    private $whitelistCollectionFactory;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;


    /**
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param \Coke\Whitelist\Model\ModuleConfig $config
     * @param CollectionFactory $whitelistCollectionFactory
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ModuleConfig $config,
        CollectionFactory $whitelistCollectionFactory,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->whitelistCollectionFactory = $whitelistCollectionFactory;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    public function getPledgeLabel($sku)
    {
        return str_replace(PHP_EOL,' ____ ', $this->getPledge($sku)->getLabel());
    }

    public function getPledgeName($sku)
    {
        return $this->getPledge($sku)->getName();
    }

    private function getPledge($sku)
    {
        $product = $this->productRepository->get($sku);

        $productOptions = $product->getOptions();
        $whitelistTypeId = null;

        foreach ($productOptions as $productOption) {
            if ($productOption->getStepId() == 1) {
                $whitelistTypeId = $productOption->getWhitelistTypeId();
            }
        }

        if ($whitelistTypeId) {
            $whitelistType = $this->whitelistTypeRepository->getById($whitelistTypeId);
        }

        return $whitelistType;
    }

    /**
     * Determine whether text has already been denied in a whitelist.
     *
     * @param $whiteListTypeId
     * @param $text
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isTextDenied($whiteListTypeId, $text)
    {
        $collection = $this->whitelistCollectionFactory->create();

        $collection
            ->addFilter('type_id', $whiteListTypeId)
            ->addFilter('value', $text)
            ->addFilter('status', WhitelistStatus::DENIED)
            ->addFilter('store_id',  $this->storeManager->getStore()->getId());

        return $collection->load()->getSize() == 1;
    }

    /**
     * Ensure text does not include any of the illegal characters speecified in system config.
     *
     * @param $text
     * @return bool
     */
    public function validateText($text)
    {
        $illegalCharacters = $this->config->getIllegalCharacters();

        if (empty($illegalCharacters)) {
            return true;
        }

        return strpbrk($text, $illegalCharacters) === false;
    }

    /**
     * @param array $productOptions
     * @return array
     */
    public function getWhitelistValuesFromProductOptions(array $productOptions): array
    {
        $whitelistValues = [];
        foreach ($productOptions as $productOption) {
            $data = $this->serializer->unserialize($productOption);
            if (isset($data['options'])) {
                foreach ($data['options'] as $option) {
                    if (isset($option['option_type'], $option['option_type'], $option['value'])
                        && str_contains($option['option_type'], 'whitelist')) {
                        $whitelistValues[] = $option['value'];
                    }
                }
            }
        }

        return $whitelistValues;
    }

    /**
     * @param string $value
     * @throws WhitelistEntityMaxLengthException
     */
    public function validateMaxLength(string $value)
    {
        if (($maxLengthValidation = $this->getMaxLengthValidation($value))) {
            if (mb_strlen($value) > $maxLengthValidation) {
                $validationMessage = ($this->getLocaleValidationMessage($value))
                    ? $this->getLocaleValidationMessage($value)
                    : 'Please do not enter more than %1 characters.';
                throw new WhitelistEntityMaxLengthException(
                    __($validationMessage, $maxLengthValidation)
                );
            }
        }
    }

    /**
     * @param string $value
     * @return int|null
     */
    private function getMaxLengthValidation(string $value): ?int
    {
        if (!($englishMaxLength = $this->config->getEnglishMaxLength())
            || !($japaneseMaxLength = $this->config->getJapaneseMaxLength())) {

            return null;
        }

        $maxLengthValidation = [
            'en' => $englishMaxLength,
            'jp' => $japaneseMaxLength
        ];
        $locale = preg_match_all('/^[ A-Za-z0-9!]*$/', $value) ? 'en' : 'jp';

        if (isset($maxLengthValidation[$locale])) {
            return $maxLengthValidation[$locale];
        }

        return null;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function getLocaleValidationMessage(string $value): ?string
    {
        $localeValidationMessage = [
            'en' => 'Please do not enter more than %1 alphabet characters.',
            'jp' => 'Please do not enter more than %1 Japanese characters.'
        ];
        $locale = preg_match_all('/^[ A-Za-z0-9!]*$/', $value) ? 'en' : 'jp';

        if (isset($localeValidationMessage[$locale])) {
            return $localeValidationMessage[$locale];
        }

        return null;
    }

    /**
     * @param $typeId
     * @param $value
     * @param $storeId
     * @return string|null
     */
    public function getWhitelistValueStatus($typeId, $value, $storeId): ?string
    {
        /** @var \Coke\Whitelist\Model\ResourceModel\Whitelist\Collection $collection */
        $collection = $this->whitelistCollectionFactory->create();
        $collection->addFilter('store_id', $storeId)
            ->addFilter('type_id', $typeId)
            ->addFieldToFilter('value', ['eq' => $value])
            ->addFieldToSelect('status');

        return ($collection->getFirstItem() && $collection->getFirstItem()->getData('status'))
            ? $this->resolveWhitelistStatus($collection->getFirstItem()->getData('status'))
            : null;
    }

    /**
     * @param null $whitelistStatus
     * @return mixed|null
     */
    public function resolveWhitelistStatus($whitelistStatus = null)
    {
        return (!$whitelistStatus || $whitelistStatus == 2) ? null : $whitelistStatus;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param int $optionId
     * @return string|null
     */
    public function getWhitelistTypeIdFromOptionId(int $optionId): ?string
    {
        $connection = $this->getConnection();
        $query = $connection->select()->from(
            $connection->getTableName('catalog_product_option'),
            'whitelist_type_id'
        )->where('option_id = ?', $optionId);

        return $connection->fetchOne($query);
    }
}
