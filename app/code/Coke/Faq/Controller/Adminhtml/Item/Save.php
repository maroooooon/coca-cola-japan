<?php

namespace Coke\Faq\Controller\Adminhtml\Item;

class Save
    extends \Coke\Faq\Controller\Adminhtml\Item
{
    const URL_KEY_MAX_LENGTH = 20;

    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Coke\Faq\Api\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Coke\Faq\Api\ItemRepositoryInterface $itemRepository
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Coke\Faq\Api\ItemRepositoryInterface $itemRepository,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ){
        $this->itemRepository = $itemRepository;
        $this->sessionFactory = $sessionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Get URL Key max length value
     *
     * @return integer
     */
    protected function getUrlKeyMaxLength()
    {
        return self::URL_KEY_MAX_LENGTH;
    }

    /**
     * Validate item before saving it
     *
     * @param [] $data
     *
     * @return []
     */
    protected function validate($data)
    {
        // Init result redirect instance
        $resultRedirect = $this->resultRedirectFactory->create();

        // Init item
        $item = false;

        // Get ID from request
        $id = $this->getRequest()
                   ->getParam('entity_id');

        if ($id) {
            $item = $this->itemRepository->get($id);

            if (!$item->getEntityId()) {
                $this->messageManager->addError(__('This FAQ Item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $item = $this->itemRepository->create();

             // Validate URL Key
            if (!isset($data['url_key']) || is_null($data['url_key']) || $data['url_key'] == '') {
                $this->messageManager->addError(__('URL Key is required.'));
                $item = false;
            } else if (strlen($data['url_key']) > $this->getUrlKeyMaxLength()) {
                $this->messageManager->addError(__('URL Key must be shorter than 20 chars.'));
                $item = false;
            } else if (preg_match('/[^a-zA-Z0-9\.]/', $data['url_key'])) {
                $this->messageManager->addError(__('URL Key must have only alpha or number chars.'));
                $item = false;
            } else {
                $itemSearch = $this->itemRepository->getByUrlKey($data['url_key']);

                foreach($itemSearch->getItems() as $itemByUrlKey) {
                    if ($itemByUrlKey && (!$item->getEntityId() || $item->getEntityId() != $itemByUrlKey->getEntityId())) {
                        $this->messageManager->addError(__('URL Key already in use by: '.$itemByUrlKey->getTitle()));
                        $item = false;
                    }
                }
            }
        }

        return $item;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        // check if data sent
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            // Validate data
            $item = $this->validate($data);

            // Check for errors
            $errorFlag = false;
            if ($item === false) {
                // Error
                $this->sessionFactory->create()->setFormData($data);
                $errorFlag = true;
            }

            // try to save it
            try {
                if (!$errorFlag) {
                    // Get ID from request
                    $id = $this->getRequest()
                               ->getParam('entity_id');
                    // init model and set data
                    $item->setTitle($data["title"]);
                    $item->setDescription($data["description"]);
                    //TODO: Hidden for future developments
//                    $item->setTags($data["tags"])
//                    $item->setMostFrequently($data["most_frequently"]);
                    $item->setFaqCategoryId($data["faq_category_id"]);
                    //Not save url_key in edit mode
                    if (!$id){
                        $item->setUrlKey($data["url_key"]);
                    }
                    $item->setSortOrder($data["sort_order"]);
                    $item->setIsActive($data["is_active"]);

                    // save the data
                    $this->itemRepository->save($item);

                    // display success message
                    $this->messageManager->addSuccess(__('You saved the FAQ Item.'));

                    // clear previously saved data from session
                    $this->sessionFactory->create()->setFormData(false);

                    // go to grid
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                $errorFlag = true;

                // display error message
                $this->messageManager->addError($e->getMessage());

                // save data in session
                $this->sessionFactory->create()->setFormData($data);

            }

        }

        // If error
        if ($errorFlag) {
            // redirect to edit form
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}