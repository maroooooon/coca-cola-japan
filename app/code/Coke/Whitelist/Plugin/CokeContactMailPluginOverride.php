<?php

namespace Coke\Whitelist\Plugin;

use Coke\Contact\Helper\Data;
use Coke\Contact\Plugin\Model\MailPlugin;
use Coke\Whitelist\Helper\Contact as ContactHelper;
use Coke\Whitelist\Model\WhiteListHelper;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\Mail;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CokeContactMailPluginOverride extends MailPlugin
{
    /**
     * @var ConfigInterface
     */
    private $contactsConfig;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var WhiteListHelper
     */
    private $whiteListHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ContactHelper
     */
    private $contactHelper;

    /**
     * CokeContactMailPluginOverride constructor.
     * @param ConfigInterface $contactsConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param WhiteListHelper $whiteListHelper
     * @param LoggerInterface $logger
     * @param ContactHelper $contactHelper
     */
    public function __construct(
        ConfigInterface $contactsConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        Data $helper,
        WhiteListHelper $whiteListHelper,
        LoggerInterface $logger,
        ContactHelper $contactHelper
    ) {
        parent::__construct($contactsConfig, $transportBuilder, $inlineTranslation, $storeManager, $helper);
        $this->contactsConfig = $contactsConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->whiteListHelper = $whiteListHelper;
        $this->logger = $logger;
        $this->contactHelper = $contactHelper;
    }


    public function aroundSend(Mail $subject, \Closure $proceed, string $replyTo, array $variables)
    {
        $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;

        $date = $this->helper->getDateForEmail();
        if($this->storeManager->getStore()->getCode() == "jp_marche_ja"){
            $subject = __('My Coke Store: We have received an inquiry');
        }else{
            $subject = 'Coca-Cola Delivery | ' . $variables['data']->getNatureOfInquiry() . ' | ' . $date;
        }
        $variables['data']->setData('subject', $subject);

        $variables['data']->setData('whitelist_request', '');

        try {
            switch ($variables['data']->getData('whitelist_request_type')) {
                case 'name' :
                    $variables['data']->setData('whitelist_request', (string)__('add the new Name'));
                    break;
                case 'pledge' :
                    $variables['data']->setData('whitelist_request',
                        (string)__('add a new Pledge for resolution %1', $this->whiteListHelper->getPledgeName($variables['data']->getData('whitelist_sku')))
                    );
                    break;
            }
        } catch (\Exception $e) {
            $this->logger->error('Contact from whitelist error:' . $e->getMessage());
        } catch (\TypeError $e) {
            $this->logger->error('Contact from whitelist error:' . $e->getMessage());
        }


        $this->inlineTranslation->suspend();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->contactsConfig->emailTemplate())
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars($variables)
                ->setFrom($this->contactsConfig->emailSender())
                ->addTo($this->contactsConfig->emailRecipient())
                ->setReplyTo($replyTo, $replyToName);
            $this->addCcEmail($transport);
            $transport->getTransport()
                ->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * @param TransportBuilder $transport
     * @return TransportBuilder
     */
    private function addCcEmail(TransportBuilder $transport): TransportBuilder
    {
        if ($ccEmails = $this->contactHelper->getCcEmail()) {
            foreach ($ccEmails as $ccEmail) {
                $transport->addCc($ccEmail);
            }
        }

        return $transport;
    }
}
