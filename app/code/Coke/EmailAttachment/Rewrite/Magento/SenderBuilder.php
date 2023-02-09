<?php

/**
 * @category FortyFour
 * @copyright Copyright (c) 2020 FortyFour LLC
 */

declare(strict_types=1);

namespace Coke\EmailAttachment\Rewrite\Magento;

use Coke\EmailAttachment\Model\PdfConfig;
use Magento\Sales\Model\Order\Email\SenderBuilder as MagentoSenderBuilder;

/**
 * Class SenderBuilder
 */
class SenderBuilder extends MagentoSenderBuilder
{
    /**
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function send()
    {
        $pdfAttachment = $this->templateContainer->getPdfAttachment();
        if ($pdfAttachment) {
            $this->transportBuilder->addAttachment($pdfAttachment, $this->getFileName(), 'application/pdf');
        }

        parent::send();
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return 'invoice-' . date('Y-m-d-His') . '.pdf';
    }
}
