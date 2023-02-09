<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2020 Classy Llama Studios, LLC
 */

namespace Zendesk\Zendesk\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zendesk\API\Exceptions\AuthException;
use Zendesk\API\Exceptions\ApiResponseException;

class Sunshine extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string $_endpoint
     */
    public $_endpoint;

    /**
     * @var Instance $instanceHelper
     */
    protected $instanceHelper;

    const IDENTIFIER = 'Magento';
    const PROFILE_TYPE = 'customer';
    /**
     * Sunshine constructor.
     * @param Context $context
     * @param Instance $instanceHelper
     */
    public function __construct(
        Context $context,
        Instance $instanceHelper
    )
    {
        parent::__construct($context);
        $this->instanceHelper = $instanceHelper;
    }

    /**
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function getTypes()
    {
        $this->_endpoint = 'api/Zendesk/relationships/types';
        return $this->all();
    }

    /**
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function getUsers()
    {
        $this->_endpoint = 'api/v2/users';
        return $this->all();
    }

    /**
     * @param $data
     * @param string $scopeType
     * @param null $scopeCode
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function post($data, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->post($this->_endpoint, $data);
    }

    public function postProfile($data, $email, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $type = $data['profile']['type'];
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->put("api/v2/user_profiles?identifier=" . self::IDENTIFIER . ":$type:email:$email", $data);
    }

    /**
     * @param $key
     * @param $data
     * @param string $scopeType
     * @param null $scopeCode
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function put($key, $data, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->put($this->_endpoint . "/$key", $data);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function all($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->get($this->_endpoint);
    }

    /**
     * @param $key
     * @param string $scopeType
     * @param null $scopeCode
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function get($key, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->get($this->_endpoint . "/$key");
    }

    /**
     * @param $key
     * @param string $scopeType
     * @param null $scopeCode
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws AuthException
     */
    public function delete($key, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        $apiClient = $this->instanceHelper->getZendeskApiInstance($scopeType, $scopeCode);
        return $apiClient->get($this->_endpoint . "/$key");
    }

}
