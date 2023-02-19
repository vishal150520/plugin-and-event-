<?php
 
namespace Bluethink\Verify\Observer\Product;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class Data implements ObserverInterface
{
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customer=$observer->getEvent()->getData(key: 'customer');
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/mycustom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(message:'Customer details');
        $logger->info($customer->getEmail());
        // $logger->info($customer->getFistname());
    }
}