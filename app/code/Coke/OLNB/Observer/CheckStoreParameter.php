<?php

namespace Coke\OLNB\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class CheckStoreParameter implements ObserverInterface
{
    /**
     * @var array
     */
    protected $storeRelationships = [
        ['fr' => 'belgium_luxembourg_french', 'nl' => 'belgium_luxembourg_dutch'],
        ['ie' => 'northern_ireland_english', 'gb' => 'great_britain_english']
    ];

    /**
     * @var ActionFlag
     */
    private $actionFlag;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var UrlHelper
     */
    private $urlHelper;
    /**
     * @var PostHelper
     */
    private $postDataHelper;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * CheckStoreParameter constructor.
     * @param ActionFlag $actionFlag
     * @param StoreManagerInterface $storeManager
     * @param UrlHelper $urlHelper
     * @param PostHelper $postDataHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ActionFlag $actionFlag,
        StoreManagerInterface $storeManager,
        UrlHelper $urlHelper,
        PostHelper $postDataHelper,
        UrlInterface $urlBuilder
    ) {
        $this->actionFlag = $actionFlag;
        $this->storeManager = $storeManager;
        $this->urlHelper = $urlHelper;
        $this->postDataHelper = $postDataHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getData('request');
        $lang = $request->getParam('__lang');

        if (!$lang) {
//            $this->redirectToHomepage($observer);
            return;
        }

        $currentStoreCode = $this->storeManager->getStore()->getCode();
        $correctRelationship = null;

        foreach ($this->storeRelationships as $relationship) {
            if (in_array($currentStoreCode, $relationship)) {
                $correctRelationship = $relationship;
                break;
            }
        }

        if (!$correctRelationship || !isset($correctRelationship[$lang])) {
            $this->redirectToHomepage($observer);
            return;
        }

        $this->changeStore($observer, $this->storeManager->getStore($correctRelationship[$lang]));
    }

    public function redirectToHomepage(Observer $observer): void
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $observer->getEvent()
            ->getData('controller_action')
            ->getResponse()
            ->setRedirect($this->storeManager->getStore()->getBaseUrl());
    }

    public function changeStore(Observer $observer, StoreInterface $store): void
    {
        $urlOnTargetStore = $this->removeLangParameter($store->getCurrentUrl(false));

        $data = [
            StoreManagerInterface::PARAM_NAME => $store->getCode(),
            '___from_store' => $this->storeManager->getStore()->getCode(),
            ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($urlOnTargetStore),
        ];

        $url = $this->urlBuilder->getUrl('stores/store/redirect', $data);

        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $observer->getEvent()
            ->getData('controller_action')
            ->getResponse()
            ->setRedirect($url);
    }

    public function removeLangParameter(string $url): string
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl['query']) {
            return $url;
        }

        $query = [];
        parse_str($parsedUrl['query'], $query);

        if (empty($query) || !isset($query['__lang'])) {
            return $url;
        }

        unset($query['__lang']);
        $parsedUrl['query'] = http_build_query($query);

        $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}