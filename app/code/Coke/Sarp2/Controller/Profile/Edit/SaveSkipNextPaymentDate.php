<?php
namespace Coke\Sarp2\Controller\Profile\Edit;

use Aheadworks\Sarp2\Api\Data\ProfileInterface;
use Aheadworks\Sarp2\Api\ProfileManagementInterface;
use Aheadworks\Sarp2\Controller\Profile\AbstractProfile;
use Aheadworks\Sarp2\Helper\Validator\DateValidator;
use Aheadworks\Sarp2\Model\DateTime\FormatConverter;
use Coke\Sarp2\Model\Profile\View\Action\Permission as CokeSarp2ActionPermission;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Sarp2\Api\ProfileRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Aheadworks\Sarp2\Model\Profile\View\Action\Permission as ActionPermission;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class SaveNextPaymentDate
 */
class SaveSkipNextPaymentDate extends AbstractProfile
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var ProfileManagementInterface
     */
    private $profileManagement;

    /**
     * @var FormatConverter
     */
    private $dateFormatConverter;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var DateValidator
     */
    private $dateValidator;
    /**
     * @var CokeSarp2ActionPermission
     */
    private $cokeSarp2ActionPermission;

    /**
     * @param Context $context
     * @param ProfileRepositoryInterface $profileRepository
     * @param Session $customerSession
     * @param Registry $registry
     * @param ActionPermission $actionPermission
     * @param FormKeyValidator $formKeyValidator
     * @param ProfileManagementInterface $profileManagement
     * @param TimezoneInterface $localeDate
     * @param FormatConverter $dateFormatConverter
     * @param DateValidator $dateValidator
     */
    public function __construct(
        Context $context,
        ProfileRepositoryInterface $profileRepository,
        Session $customerSession,
        Registry $registry,
        ActionPermission $actionPermission,
        FormKeyValidator $formKeyValidator,
        ProfileManagementInterface $profileManagement,
        TimezoneInterface $localeDate,
        FormatConverter $dateFormatConverter,
        DateValidator $dateValidator,
        CokeSarp2ActionPermission $cokeSarp2ActionPermission
    ) {
        parent::__construct($context, $profileRepository, $customerSession, $registry, $actionPermission);
        $this->formKeyValidator = $formKeyValidator;
        $this->profileManagement = $profileManagement;
        $this->localeDate = $localeDate;
        $this->dateFormatConverter = $dateFormatConverter;
        $this->dateValidator = $dateValidator;
        $this->cokeSarp2ActionPermission = $cokeSarp2ActionPermission;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $this->validate($data);
                $profile = $this->performSave($data);
                $this->messageManager->addSuccessMessage(__('Next Payment Date has been successfully changed.'));
                return $resultRedirect->setPath('*/*/index', $this->getParams($profile->getProfileId()));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while updating Next Payment Date.')
                );
            }
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function isActionAllowed()
    {
        $profileId = $this->getProfile()->getProfileId();
        return $this->cokeSarp2ActionPermission->isSkipNextPaymentDateActionAvailable($profileId);
    }

    /**
     * Validate form
     *
     * @param array $data
     * @throws LocalizedException
     */
    private function validate($data)
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key. Please refresh the page.'));
        }
        $nextPaymentDate = $data['next-payment-date'];
        $format = $this->dateFormatConverter->convertToDateTimeFormat();
        if (!$this->dateValidator->isValid($nextPaymentDate, $format)) {
            throw new  LocalizedException(__('Sorry, we are unable to skip this payment date.'));
        }
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return ProfileInterface
     * @throws LocalizedException
     * @throws NotFoundException
     */
    private function performSave($data)
    {
        $profile = $this->getProfile();
        $nextPaymentDate = $data['next-payment-date'];
        $newNextPaymentDate = \DateTime::createFromFormat(
            $this->dateFormatConverter->convertToDateTimeFormat(),
            $nextPaymentDate,
            new \DateTimeZone($this->localeDate->getConfigTimezone())
        );
        $newNextPaymentDate = $this->localeDate->date($newNextPaymentDate, null, false);
        $newNextPaymentDate = $newNextPaymentDate->format(DateTime::DATETIME_PHP_FORMAT);

        return $this->profileManagement->changeNextPaymentDate($profile->getProfileId(), $newNextPaymentDate);
    }
}
