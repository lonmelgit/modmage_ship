<?php

namespace ModMage\Ship\Model\ResourceModel\Ways;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'ship_ways_collection';
    protected $_eventObject = 'ways_collection';

    public function _construct()
    {
        $this->_init(
            'ModMage\Ship\Model\Ways',
            'ModMage\Ship\Model\ResourceModel\Ways'
        );
    }
}
