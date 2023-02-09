<?php

namespace Coke\CompletedOrderQuestionnaire\Model\Email\Sender;

use Aheadworks\Sarp2\Model\Profile;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Sender
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var IdentityInterface
     */
    protected $identityContainer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Template
     */
    protected $templateContainer;

    /**
     * Sender constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param IdentityInterface $identityContainer
     * @param StoreManagerInterface $storeManager
     * @param Template $templateContainer
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        IdentityInterface $identityContainer,
        StoreManagerInterface $storeManager,
        Template $templateContainer
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->identityContainer = $identityContainer;
        $this->storeManager = $storeManager;
        $this->templateContainer = $templateContainer;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->identityContainer->isEnabled();
    }

    /**
     * @param $params
     * @param string $recipientEmail
     * @return bool
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendEmail($params, string $recipientEmail)
    {
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }

        $this->configureEmailTemplate($params);
        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }

        $transport = $this->transportBuilder->addTo($recipientEmail)
            ->getTransport();
        $transport->sendMessage();

        if ($this->identityContainer->getCopyMethod() == 'copy') {
            try {
                $this->sendCopyTo($params);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * Prepare and send copy email message
     *
     * @param $params
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendCopyTo($params)
    {
        $copyTo = $this->identityContainer->getEmailCopyTo();

        if (!empty($copyTo)) {
            foreach ($copyTo as $email) {
                $this->configureEmailTemplate($params);
                $this->transportBuilder->addTo($email);
                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();
            }
        }
    }

    /**
     * Configure email template
     *
     * @param array $params
     * @return void
     * @throws MailException
     * @throws NoSuchEntityException
     */
    protected function configureEmailTemplate(array $params)
    {
        $this->transportBuilder->setTemplateIdentifier($this->identityContainer->getTemplateId());
        $this->transportBuilder->setTemplateOptions($this->getTemplateOptions());
        $this->transportBuilder->setTemplateVars($params);
        $this->transportBuilder->setFromByScope(
            $this->identityContainer->getEmailIdentity(),
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Get template options.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getTemplateOptions(): array
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];
    }
}
