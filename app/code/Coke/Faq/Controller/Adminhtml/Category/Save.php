<?php

namespace Coke\Faq\Controller\Adminhtml\Category;

class Save
    extends \Coke\Faq\Controller\Adminhtml\Category
{
    const URL_KEY_MAX_LENGTH = 20;

    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Coke\Faq\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Coke\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    ){
        $this->sessionFactory = $sessionFactory;
        $this->categoryRepository = $categoryRepository;
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
     * Validate category before saving it
     *
     * @param [] $data
     *
     * @return []
     */
    protected function validateCategory($data)
    {
        // Init result redirect instance
        $resultRedirect = $this->resultRedirectFactory->create();

        // Init category
        $category = false;

        // Get ID from request
        $id = $this->getRequest()
                   ->getParam('entity_id');

        if ($id) {
            $category = $this->categoryRepository->get($id);

            if (!$category->getEntityId()) {
                $this->messageManager->addError(__('This FAQ Category no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $category = $this->categoryRepository->create();
        }

        // Validate URL Key
        if (!isset($data['url_key']) || is_null($data['url_key']) || $data['url_key'] == '') {
            $this->messageManager->addError(__('URL Key is required.'));
            $category = false;
        } else if (strlen($data['url_key']) > $this->getUrlKeyMaxLength()) {
            $this->messageManager->addError(__('URL Key must be shorter than 20 chars.'));
            $category = false;
        } else if (preg_match('/[^a-zA-Z0-9\.]/', $data['url_key'])) {
            $this->messageManager->addError(__('URL Key must have only alpha or number chars.'));
            $category = false;
        } else {
            $categorySearch = $this->categoryRepository->getByUrlKey($data['url_key']);

            foreach($categorySearch->getItems() as $categoryByUrlKey) {
                if ($categoryByUrlKey && (!$category->getEntityId() || $category->getEntityId() != $categoryByUrlKey->getEntityId())) {
                    $this->messageManager->addError(__('URL Key already in use by: '.$categoryByUrlKey->getName()));
                    $category = false;
                }
            }
        }

        return $category;
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
            // Validate category data
            $category = $this->validateCategory($data);

            // Check for errors
            $errorFlag = false;
            if ($category === false) {
                // Error
                $this->sessionFactory->create()->setFormData($data);
                $errorFlag = true;
            }

            // try to save it
            try {
                if (!$errorFlag) {
                    // init model and set data
                    $category->setName($data["name"]);
                    $category->setIsActive($data["is_active"]);
                    $category->setStoreId($data["store_id"]);
                    $category->setSortOrder($data["sort_order"]);
                    $category->setUrlKey($data["url_key"]);

                    // save the data
                    $this->categoryRepository->save($category);

                    // display success message
                    $this->messageManager->addSuccess(__('You saved the FAQ Category.'));

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