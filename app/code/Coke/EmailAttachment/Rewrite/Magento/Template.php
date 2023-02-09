<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Rewrite\Magento;

use Magento\Sales\Model\Order\Email\Container\Template as MagentoTemplate;

/**
 * Class Template
 */
class Template extends MagentoTemplate
{
    /**
     * @var string
     */
    protected $pdfAttachment = '';

    /**
     * @param string $pdfAttachment
     */
    public function setPdfAttachment(string $pdfAttachment): void
    {
        $this->pdfAttachment = $pdfAttachment;
    }

    /**
     * @return string
     */
    public function getPdfAttachment(): string
    {
        return $this->pdfAttachment;
    }
}
