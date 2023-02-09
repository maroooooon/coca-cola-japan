<?php

namespace FortyFour\AgeRestriction\Model\App\Action;

use FortyFour\AgeRestriction\Helper\AgeRestrictionContext;
use FortyFour\AgeRestriction\Helper\Config as AgeRestrictionConfig;
use FortyFour\AgeRestriction\Helper\Cookie as AgeRestrictionCookieHelper;
use Magento\Framework\App\Http\Context as HttpContext;
use Psr\Log\LoggerInterface;

class ContextPlugin
{
    /**
     * @var HttpContext
     */
    private $httpContext;
    /**
     * @var AgeRestrictionCookieHelper
     */
    private $ageRestrictionCookieHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var AgeRestrictionConfig
     */
    private $config;

    /**
     * ContextPlugin constructor.
     * @param HttpContext $httpContext
     * @param AgeRestrictionCookieHelper $ageRestrictionCookieHelper
     * @param LoggerInterface $logger
     * @param AgeRestrictionConfig $config
     */
    public function __construct(
        HttpContext $httpContext,
        AgeRestrictionCookieHelper $ageRestrictionCookieHelper,
        LoggerInterface $logger,
        AgeRestrictionConfig $config
    ) {
        $this->httpContext = $httpContext;
        $this->ageRestrictionCookieHelper = $ageRestrictionCookieHelper;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function beforeDispatch()
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $defaultValue = '';
        $cookieValue = $this->ageRestrictionCookieHelper->getCookie(
            AgeRestrictionCookieHelper::AGE_RESTRICTION_COOKIE_NAME
        );
        $this->httpContext->setValue(
            AgeRestrictionContext::CONTEXT_AGE_RESTRICTION_COOKIE,
            $cookieValue,
            $defaultValue
        );

        $this->logger->info(__(
            "[ContextPlugin::beforeDispatch()] Setting '%1' as the age restriction cookie context value.",
            $this->httpContext->getValue(AgeRestrictionContext::CONTEXT_AGE_RESTRICTION_COOKIE)
        ));
    }
}
