<?php

namespace CokeJapan\Hccb\Plugin\Webapi;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Webapi\Response;

class ResponsePlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param Response $subject
     * @param $result
     * @return mixed
     */
    public function afterGetMessages(Response $subject, $result) {

        $pathInfo = $this->request->getPathInfo();
        $body = $subject->getBody();
        if (strpos($pathInfo, 'V1/hccb/shipment') !== false &&
            strpos($body, 'order_skipped') !== false) {
            $subject->setStatusCode(202);
        }
        return $result;
    }
}
