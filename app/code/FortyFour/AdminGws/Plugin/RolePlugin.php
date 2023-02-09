<?php

namespace FortyFour\AdminGws\Plugin;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\View;
use Psr\Log\LoggerInterface;

class RolePlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;
    /**
     * @var View
     */
    private $view;

    /**
     * RolePlugin constructor.
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param View $view
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        View $view
    ) {
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->view = $view;
    }

    /**
     * @param \Magento\AdminGws\Model\Role $subject
     * @param $role
     * @return array
     */
    public function beforeSetAdminRole(
        \Magento\AdminGws\Model\Role $subject,
                                     $role
    ) {
        $defaultHandle = $this->view->getDefaultLayoutHandle();
        if ($defaultHandle == 'catalog_product_save') {
            $role->setData('gws_is_all', 1);
        }

        return [$role];
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @return string
     */
    private function getGwsIsAll($role)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $connection->getTableName('authorization_role'),
            'gws_is_all'
        )->where('role_id = ?', $role->getId());

        return $connection->fetchOne($select) ?? 0;
    }
}
