<?php


namespace Coke\Whitelist\Plugin;


use Coke\Whitelist\Model\ModuleConfig;
use Magento\Catalog\Block\Product\View\Options\Type\Text;

class WhiteListTemplatePlugin
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    public function __construct(
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }

    public function beforeToHtml(Text $object)
    {
        if(!$this->moduleConfig->isEnabled()) {
            return $this;
        }

        $_option = $object->getOption();

        switch ($_option->getStepId()){
            case 1: $object->setTemplate('Coke_Whitelist::product/view/options/type/white_lits_dropdown.phtml'); break;
//            case 2: $object->setTemplate('Coke_Whitelist::product/view/options/type/white_lits_text.phtml'); break;
        }
    }
}
