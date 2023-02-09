<?php

namespace Coke\TaxImportExport\Plugin\Adminhtml;

class ImportExportPlugin
{
    /**
     * @var string
     */
    private $_template = 'Coke_TaxImportExport::importExport.phtml';

    /**
     * @param \Magento\TaxImportExport\Block\Adminhtml\Rate\ImportExport $subject
     * @return string
     */
    public function afterGetTemplate()
    {
        return $this->_template;
    }
}
