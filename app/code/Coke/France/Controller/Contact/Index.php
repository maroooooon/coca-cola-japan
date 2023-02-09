<?php
namespace Coke\France\Controller\Contact;

use Coke\ContactAgeRestrict\Helper\Data as ContactAgeRestrictHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Index
 *
 * @package Coke\ThemeControl\Controller\Contact
 */
class Index extends Action
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ContactAgeRestrictHelper
     */
    protected $contactAgeRestrictHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $sessionManager;

    public function __construct(
        Context $context,
        ContactAgeRestrictHelper $contactAgeRestrictHelper,
        Session $sessionManager,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->messageManager = $context->getMessageManager();
        $this->sessionManager = $sessionManager;
        $this->contactAgeRestrictHelper = $contactAgeRestrictHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        try {
            $dob = new \DateTime($this->getFormattedDate());
            $this->contactAgeRestrictHelper->validateAge($dob);
            $this->dispatchEmail();
            $this->messageManager->addSuccessMessage('Votre message a été envoyé');
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect;
    }

    /**
     * Send email to the correct customer service representative
     *
     * @return $this
     */
    private function dispatchEmail()
    {
        $store = $this->storeManager->getStore()->getId();

        $recipient = $this->getRecipient($this->getRequest()->getParam('nature'));

        $transport = $this->transportBuilder->setTemplateIdentifier('coke_contact_request')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($this->getRequest()->getParams())
            ->setFrom('general')
            ->addBcc('minnguyen@coca-cola.com', 'Coke France Alerts')
            ->addBcc('coke_customerservice@fortyfour.com', 'Coke France Alerts')
            ->addTo($recipient, 'Coke France')
            ->getTransport();
        $transport->sendMessage();

        return $this;
    }

    /**
     * Return the formatted date for DOB validation
     *
     * @return string
     */
    private function getFormattedDate()
    {
        $request = $this->getRequest();

        return sprintf('%s-%s-%s',
            $request->getParam('sYear'),
            $request->getParam('sMonth'),
            $request->getParam('sMonth')
        );
    }

    /**
     * Get the correct email recipient for contact form submissions
     *
     * @todo This should be a dynamic field within Magento
     * @param string $reason
     * @return string Email Address
     */
    private function getRecipient($reason)
    {
        $reasons = array(
            'Ajouter un prénom / un surnom / une expression' => 'fr.ciccontact@coca-cola.com',
            'Etat d\'avancement de ma commande' => 'cocacolastore@tessi.fr',
            'Modification de ma commande' => 'cocacolastore@tessi.fr',
            'Réception de mon colis' => 'cocacolastore@tessi.fr',
            'Signaler un problème avec ma commande' => 'cocacolastore@tessi.fr',
            'Renseignements sur Coca Cola' => 'fr.ciccontact@coca-cola.com',
            'Demande de partenariat Coca-Cola' => 'fr.ciccontact@coca-cola.com'
        );

        return $reasons[$reason];
    }
}
