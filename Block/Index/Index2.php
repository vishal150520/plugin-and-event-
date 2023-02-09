<?php

namespace Bluethink\Verify\Block\Index;

class Index2 extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}
	public function msg2()
    {
        echo "hello2";
    }
}