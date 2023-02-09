<?php

namespace Coke\AdminTimezone\Plugin\App\Config;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;

class OverrideTimezone
{
    /**
     * @var State
     */
    protected $state;

    /**
     * OverrideTimezone constructor.
     * @param State $state
     */
    public function __construct(
        State $state
    )
    {
        $this->state = $state;
    }

    public function overrideTimezone()
    {
        $authSession = ObjectManager::getInstance()->get(Session::class);
        $user = $authSession->getUser();
        if (!$user) {
            return false;
        }

        return $this->overrideTimezoneForUser($user);
    }

    public function overrideTimezoneForUser($user)
    {
        if (($tz = $user->getData('locale_timezone'))) {
            return $tz;
        }

        return false;
    }

    public function aroundGet(
        Config $subject,
        callable $proceed,
        $configType, $path = '', $default = null
    )
    {
        if ($this->state->getAreaCode() === Area::AREA_ADMINHTML) {
            if ($configType === 'system' && $path === 'default/general/locale/timezone') {
                $tz = $this->overrideTimezone();

                if ($tz !== false) {
                    return $tz;
                }
            }
        }

        return $proceed($configType, $path, $default);
    }
}
