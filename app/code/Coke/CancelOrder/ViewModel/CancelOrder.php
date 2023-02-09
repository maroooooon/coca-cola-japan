<?php

namespace Coke\CancelOrder\ViewModel;

use Coke\CancelOrder\Helper\CancelOrderHelper;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CancelOrder implements ArgumentInterface
{
    /**
     * @var PostHelper
     */
    private $postHelper;
    /**
     * @var CancelOrderHelper
     */
    private $cancelOrderHelper;

    /**
     * @param PostHelper $postHelper
     * @param CancelOrderHelper $cancelOrderHelper
     */
    public function __construct(
        PostHelper $postHelper,
        CancelOrderHelper $cancelOrderHelper
    ) {
        $this->postHelper = $postHelper;
        $this->cancelOrderHelper = $cancelOrderHelper;
    }

    /**
     * @return PostHelper
     */
    public function getPostHelper(): PostHelper
    {
        return $this->postHelper;
    }

    /**
     * @return CancelOrderHelper
     */
    public function getCancelOrderHelper(): CancelOrderHelper
    {
        return $this->cancelOrderHelper;
    }
}
