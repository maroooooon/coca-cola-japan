<?php

namespace Coke\AdminTimezone\Plugin\User;

use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Framework\App\RequestInterface;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\UserFactory;

class AddUserEditField
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var UserFactory
     */
    private $user;
    /**
     * @var UserResource
     */
    private $userResource;
    /**
     * @var Timezone
     */
    private $timezoneSource;

    public function __construct(
        RequestInterface $request,
        UserFactory $user,
        UserResource $userResource,
        Timezone $timezoneSource
    ){
        $this->request = $request;
        $this->user = $user;
        $this->userResource = $userResource;
        $this->timezoneSource = $timezoneSource;
    }

    /**
     * @param \Magento\User\Block\User\Edit\Tab\Main $subject
     */
    public function beforeGetFormHtml(
        \Magento\User\Block\User\Edit\Tab\Main $subject
    )
    {
        $fieldName = 'locale_timezone';

        $userId = $this->request->getParam('user_id');
        $user = $this->user->create();
        $this->userResource->load($user, $userId);

        $options = $this->timezoneSource->toOptionArray();
        array_unshift($options, [
            'label' => 'Use Default',
            'value' => '',
        ]);

        $form = $subject->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            $fieldName,
            'select',
            [
                'name' => $fieldName,
                'label' => __('Locale Timezone'),
                'id' => $fieldName,
                'title' => __('Locale Timezone'),
                'class' => 'input-select',
                'values' => $options,
            ]
        );

        $form->addValues([
            $fieldName => $user->getData($fieldName)
        ]);
    }
}
