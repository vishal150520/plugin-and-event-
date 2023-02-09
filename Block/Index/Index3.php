<?php

namespace Bluethink\Verify\Block\Index;

class Index3 extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}
	public function msg3()
    {
        echo "hello2";
    }
}