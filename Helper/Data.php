<?php

namespace Bluethink\Verify\Helper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_MODULE_CONFIG = 'thecoachsmb/general/enable';

    public $_storeManager;
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue() {
        return $this->scopeConfig->getValue(self::XML_PATH_MODULE_CONFIG,   ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getStoreId());
    }
}