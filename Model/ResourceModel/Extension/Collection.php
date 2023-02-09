<?php
namespace Bluethink\Verify\Model\ResourceModel\Extension;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Bluethink\Verify\Model\Extension', 
        'Bluethink\Verify\Model\ResourceModel\Extension');
    }
}