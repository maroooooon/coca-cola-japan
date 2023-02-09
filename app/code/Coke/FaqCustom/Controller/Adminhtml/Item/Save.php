<?php
declare(strict_types=1);

namespace Coke\FaqCustom\Controller\Adminhtml\Item;

class Save extends \Coke\Faq\Controller\Adminhtml\Item\Save
{
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
//                    if (!$id){
                    $item->setUrlKey($data["url_key"]);
//                    }
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