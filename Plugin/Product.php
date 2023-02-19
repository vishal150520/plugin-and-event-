<?php
namespace Bluethink\Verify\Plugin;
class Product
{
    public function aftergetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $result." Update Name";
    }
}