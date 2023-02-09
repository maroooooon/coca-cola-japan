<?php

namespace FortyFour\AgeRestriction\Observer;

use FortyFour\AgeRestriction\Helper\AgeRestrictionContext;
use FortyFour\AgeRestriction\Helper\Config as AgeRestrictionConfig;
use FortyFour\AgeRestriction\Helper\Cookie as AgeRestrictionCookieHelper;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class RedirectToAgeValidation implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var Http
     */
    private $httpResponse;
    /**
     * @var AgeRestrictionConfig
     */
    private $ageRestrictionConfig;
    /**
     * @var HttpContext
     */
    private $httpContext;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RedirectToAgeValidation constructor.
     * @param UrlInterface $url
     * @param Http $httpResponse
     * @param AgeRestrictionConfig $ageRestrictionConfig
     * @param HttpContext $httpContext
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $url,
        Http $httpResponse,
        AgeRestrictionConfig $ageRestrictionConfig,
        HttpContext $httpContext,
        LoggerInterface $logger
    ) {
        $this->url = $url;
        $this->httpResponse = $httpResponse;
        $this->ageRestrictionConfig = $ageRestrictionConfig;
        $this->httpContext = $httpContext;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return Http|\Magento\Framework\App\Response\HttpInterface|void
     */
    public function execute(Observer $observer)
    {
        $currentUrl = $this->url->getCurrentUrl();
        if (!$this->ageRestrictionConfig->isEnabled()) {
            $this->logger->debug(__("[RedirectToAgeValidation::execute()] age restriction not enabled."));
            return;
        }

        $ageRestrictionContext = $this->httpContext->getValue(
            AgeRestrictionContext::CONTEXT_AGE_RESTRICTION_COOKIE
        );

        $this->logger->debug(__(
            "[RedirectToAgeValidation::execute()] '%1' as the age restriction cookie context value.",
            $ageRestrictionContext
        ));

        if ($ageRestrictionContext === AgeRestrictionCookieHelper::AGE_RESTRICTION_VALID) {
            $this->logger->debug(__("[RedirectToAgeValidation::execute()] age restriction cookie is valid."));
            return;
        }

        if ($ageRestrictionContext === AgeRestrictionCookieHelper::AGE_RESTRICTION_INVALID) {
            $this->logger->debug(__("[RedirectToAgeValidation::execute()] age restriction cookie is invalid."));

            if ($this->getRoute() != 'agerestriction/invalid') {
                return $this->httpResponse->setRedirect(
                    $this->url->getUrl('agerestriction/invalid')
                );
            }

            return;
        }

        if (strpos($this->getRoute(), 'agerestriction') === false) {
            $this->logger->debug(__("[RedirectToAgeValidation::execute()] redirecting."));
            return $this->httpResponse->setRedirect(
                $this->url->getUrl(sprintf('agerestriction?referrer=%s', base64_encode($currentUrl)))
            );
        }
    }

    /**
     * @return string
     */
    private function getRoute()
    {
        $currentUrl = $this->url->getCurrentUrl();
        $baseUrl = $this->url->getBaseUrl();

        return rtrim(str_replace($baseUrl, "", $currentUrl), '/');
    }
}
