<?php

namespace Coke\Whitelist\Controller\Ajax;

use Coke\NameManagement\Api\Data\NameInterface;
use Coke\NameManagement\Api\NameRepositoryInterface;
use Coke\Whitelist\Api\WhitelistRepositoryInterface;
use Coke\Whitelist\Api\WhitelistTypeRepositoryInterface;
use Coke\Whitelist\Model\ModuleConfig;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Coke\NameManagement\Model\Autocomplete\SearchDataProvider;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface;
use Coke\Whitelist\Model\ImageGenerator;

/**
 * Class Image
 *
 * @package \Coke\NameManagement\Controller\Ajax
 */
class Image extends Action
{
    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var WhitelistTypeRepositoryInterface
     */
    private $whitelistTypeRepository;

    /**
     * @var WhitelistRepositoryInterface
     */
    private $whitelistRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlBuilder
     */
    private $imageUrlBuilder;

    /**
     * @var ProductCustomOptionRepositoryInterface
     */
    private $customOptionRepository;

    /**
     * @var ImageGenerator
     */
    private $imageGenerator;

    /**
     * Image constructor.
     * @param Context $context
     * @param ModuleConfig $config
     * @param ProductRepositoryInterface $productRepository
     * @param WhitelistTypeRepositoryInterface $whitelistTypeRepository
     * @param WhitelistRepositoryInterface $whitelistRepository
     * @param StoreManagerInterface $storeManager
     * @param UrlBuilder $imageUrlBuilder
     * @param ProductCustomOptionRepositoryInterface $customOptionRepository
     * @param ImageGenerator $imageGenerator
     */
    public function __construct(
        Context $context,
        ModuleConfig $config,
        ProductRepositoryInterface $productRepository,
        WhitelistTypeRepositoryInterface $whitelistTypeRepository,
        WhitelistRepositoryInterface $whitelistRepository,
        StoreManagerInterface $storeManager,
        UrlBuilder $imageUrlBuilder,
        ProductCustomOptionRepositoryInterface $customOptionRepository,
        ImageGenerator $imageGenerator
    )
    {
        parent::__construct($context);
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->whitelistTypeRepository = $whitelistTypeRepository;
        $this->whitelistRepository = $whitelistRepository;
        $this->storeManager = $storeManager;
        $this->imageUrlBuilder = $imageUrlBuilder;
        $this->customOptionRepository = $customOptionRepository;
        $this->imageGenerator = $imageGenerator;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $badNames = [];

        try {
            $store = $this->storeManager->getStore();

            $pledge    = $this->getRequest()->getParam('pledge');
            $pledge_2  = $this->getRequest()->getParam('pledge_2');
            $nameFrom  = $this->getRequest()->getParam('name_from');
            $nameTo    = $this->getRequest()->getParam('name_to');
            $sku       = $this->getRequest()->getParam('sku');
            $imageSize = $this->getRequest()->getParam('image_size', 'product_page_image_medium');

            $debug = !!$this->getRequest()->getParam('debug', '0');
            $renderImage = !!$this->getRequest()->getParam('renderImage', '0');
            $fieldPositions = $this->getRequest()->getParam('field_positions');

            if (!$pledge || !$sku) {
                throw new LocalizedException(__('Missing data'));
            }

            $illegalCharacters = $this->config->getIllegalCharacters();

            if (!empty($illegalCharacters)) {
                foreach ([$pledge, $pledge_2, $nameFrom, $nameTo] as $textField) {
                    if (!empty($textField) && !empty($textField['value'])) {
                        if (!$this->validateText($textField['value'], $illegalCharacters)) {
                            throw new LocalizedException(__('Bad character found. The following characteres are not allowed: %1', $illegalCharacters));
                        }
                    }
                }
            }

            $pledgeTextLines = [];

            $validatedPledge = $this->validateLine($sku, $pledge, $store->getId());
            if ($validatedPledge) {
                $pledgeTextLines = array_merge($pledgeTextLines, explode(PHP_EOL, $validatedPledge));
            } else {
                throw new LocalizedException(__('Pledge not allowed: %1', $pledge['value']));
            }

            if ($pledge_2) {
                $validatedPledge = $this->validateLine($sku, $pledge_2, $store->getId());
                if ($validatedPledge) {
                    $pledgeTextLines = array_merge($pledgeTextLines, explode(PHP_EOL, $validatedPledge));
                } else {
                    throw new LocalizedException(__('Pledge not allowed: %1', $pledge_2['value']));
                }
            }

            $nameTextLines = [];

            if ($this->config->canUpdateToAndFromOnImage()) {
                if ($nameFrom) {
                    $validatedNameFrom = $this->validateLine($sku, $nameFrom, $store->getId());
                    if ($validatedNameFrom) {
                        $nameTextLines[] = __('From %1', $validatedNameFrom);
                    } else {
                        $badNames[] = $nameFrom['value'];
                    }
                }

                if ($nameTo) {
                    $validatedNameTo = $this->validateLine($sku, $nameTo, $store->getId());
                    if ($validatedNameTo) {
                        $nameTextLines[] = __('To %1', $validatedNameTo);
                    } else {
                        $badNames[] = $nameTo['value'];
                    }
                }
            }

            $product = $this->productRepository->get($sku);
            $images = $product->getMediaGalleryImages();

            if (!$images instanceof \Magento\Framework\Data\Collection) {
                return $images;
            }

            switch (count($pledgeTextLines)) {
                case 2:
                    $imageUrl = $this->imageUrlBuilder->getUrl($images->getLastItem()->getFile(), $imageSize);
                    break;
                default:
                    $imageUrl = $this->imageUrlBuilder->getUrl($images->getFirstItem()->getFile(), $imageSize);
                    break;
            }

            if (($parsedPath = parse_url($imageUrl, PHP_URL_PATH)) !== false) {
                $imageUrl = dirname(__FILE__) . "/../../../../../../pub/". $parsedPath;
            }

            $fontType   = ($store->getCode() == 'turkey_turkish') ? 8 : 0;
            $nameOffset = $product->getData('name_pos_offset') ?? $this->getRequest()->getParam('name_offset');

            if (!is_numeric($nameOffset)) {
                $nameOffset = 80;
            }

            $encodedImage = $this->imageGenerator->generate(
                $imageUrl,
                $pledgeTextLines,
                $nameTextLines,
                $nameOffset,
                $sku,
                $this->getFieldPositions($product, $fieldPositions),
                $fontType,
                $debug
            );

            if ($encodedImage) {
                if ($renderImage) {
                    echo '<br /><Br /><img src="' . $encodedImage . '">';
                    die();
                }

                return $resultJson->setData([
                    'message' => 'success',
                    'image' => (string) $encodedImage,
                    'failed_validation' => !empty($badNames),
                    'bad_names' => $badNames,
                ]);
            } else {
                throw new LocalizedException(__("Something goes wrong"));
            }

        } catch (LocalizedException $e) {
            return $resultJson->setData([
                'failed_validation' => true,
                'message' => $e->getMessage(),
                'bad_names' => $badNames,
            ]);
        } catch (\Exception $exception) {
            return $resultJson->setData([
                'message' => 'failed',
                'data' => [
                    'exception' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'input' => json_encode($this->getRequest()->getParams())
                ]
            ]);
        }

        /** @var Json $resultJson */
        return $resultJson->setData('Something went wrong');
    }

    private function whitelistCheck($value, $storeId)
    {
        try {
            $whitelist = $this->whitelistRepository->getByValue($value, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $whitelist->getValue();
    }

    private function getCustomOption($sku, $optionId)
    {
        $customOptions = $this->customOptionRepository->getList($sku);

        foreach ($customOptions as $option) {
            if ($option->getId() == $optionId) {
                return $option;
            }
        }

        return false;
    }

    private function getFieldPositions($product, $fieldPositions)
    {
        if (($positions = $fieldPositions) || ($positions = $product->getData('personalization_fields_pos'))) {
            $fieldPositions = json_decode($positions, true);
            foreach ($fieldPositions as $i => $fieldPosition) {
                if (!isset($fieldPosition['is_found'])) {
                    $fieldPositions[$i]['is_found'] = true;
                }

                if (isset($fieldPosition['length'])) {
                    $fieldPositions[$i]['end_x'] = $fieldPositions[$i]['start_x'] + $fieldPosition['length'];
                }


                if (isset($fieldPosition['height'])) {
                    $fieldPositions[$i]['end_y'] = $fieldPositions[$i]['start_y'] + $fieldPosition['height'];
                }
            }

            return $fieldPositions;
        }

        return null;
    }

    private function validateLine($sku, $optionData, $storeId)
    {
        $option = $this->getCustomOption($sku, $optionData['option_id']);

        if (!$option || $option->getAllowNonWhitelistedValues() == false) {
            $whitelistValue = $this->whitelistCheck($optionData['value'], $storeId);

            if (!$whitelistValue) {
                return false;
            }

            return $whitelistValue;
        }

        return $optionData['value'];
    }

    private function validateText($text, $illegalCharacters): bool
    {
        return strpbrk($text, $illegalCharacters) === false;
    }
}
