<?php

namespace ModMage\Ship\Controller\Adminhtml\Ways;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;

    protected $resultPageFactory;

    public function __construct(
        Action\Context                             $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry                $registry
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create(\ModMage\Ship\Model\Ways::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This methods no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('ship_ways', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Shipping methods') : __('Add Shipping methods'),
            $id ? __('Edit Shipping methods') : __('Add Shipping methods')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping methods'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('Add shipping method'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ModMage_Ship::save');
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ModMage_Ship::ship_ways')
            ->addBreadcrumb(__('Shipping methods'), __('Shipping methods'))
            ->addBreadcrumb(__('Manage All Shipping methods'), __('Manage All Shipping methods'));
        return $resultPage;
    }
}
