<?php
namespace Bluethink\Verify\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bluethink\Verify\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getBlogs()
    {
        $data = $this->helper->getConfigValue();
        return $data;
    }
    public function getFormAction()
    {
        return $this->getUrl('verify/index/submit', ['_secure' => true]);
    }
}