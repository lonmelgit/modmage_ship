<?php

namespace ModMage\Ship\Block\Adminhtml;

class Ways extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_ways';
        $this->_blockGroup = 'ModMage_Ship';
        $this->_headerText = __('Manage Shipping methods');

        parent::_construct();

        if ($this->_isAllowedAction('ModMage_Ship::save')) {
            $this->buttonList->update('add', 'label', __('Add Names'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
