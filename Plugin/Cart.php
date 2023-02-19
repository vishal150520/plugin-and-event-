<?php
namespace Bluethink\Verify\Plugin;
class Cart
{
public function beforeaddProduct(\Magento\Checkout\Model\Cart $subject,$productInfo,$requestInfo=Null)
{
    $requestInfo['qty']=10;
    return array($productInfo,$requestInfo);
}
public function afteraddProduct(\Magento\Checkout\Model\Cart $subject,\Closure $proceed,$productInfo,$requestInfo=Null)
{
    $requestInfo['qty']=5;
    $result=$proceed($productInfo,$requestInfo);
    return $result;
}
}
