<?php

namespace FortyFour\AgeRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Psr\Log\LoggerInterface;

class AgeRestrictionContext extends AbstractHelper
{
    const CONTEXT_AGE_RESTRICTION_COOKIE = 'age_restriction_cookie';

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * Context constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param HttpContext $httpContext
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->httpContext = $httpContext;
    }

    /**
     * @return mixed|null
     */
    public function getAgeRestrictionCookie()
    {
        return $this->httpContext->getValue(self::CONTEXT_AGE_RESTRICTION_COOKIE);
    }
}
