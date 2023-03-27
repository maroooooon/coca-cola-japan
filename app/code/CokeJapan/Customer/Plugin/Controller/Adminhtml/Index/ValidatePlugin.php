<?php

namespace CokeJapan\Customer\Plugin\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index\Validate;
use Magento\Framework\App\RequestInterface;

class ValidatePlugin
{
    const TIME_ZONE = 'Asia/Tokyo';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param Validate $subject
     * @param $resultJson
     * @return mixed|void
     * @throws \Exception
     */
    public function afterExecute(Validate $subject, $resultJson)
    {
        $request = $this->request->getParams();
        if (isset($request['customer']) && isset($request['customer']['dob'])) {
            $dob = $request['customer']['dob'];
            $dobTime = strtotime($dob);
            $maxDob =  new \DateTime('13 year ago', new \DateTimeZone(self::TIME_ZONE));
            $maxDobTime = strtotime($maxDob->format('Y-m-d H:i:s'));

            if ($dobTime > $maxDobTime) {
                $response = new \Magento\Framework\DataObject();
                $response->setMessages(['無効な年齢です']);
                $response->setError(1);

                $resultJson->setData($response);
                return $resultJson;
            }
        }
    }
}
