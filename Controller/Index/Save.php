<?php

namespace Bluethink\Verify\Controller\Index;

use Magento\Framework\App\Action\Context;
use Bluethink\Test\Model\ExtensionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
class Save extends \Magento\Framework\App\Action\Action
{
    protected $_test;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
        Context $context,
        ExtensionFactory $test,
        UploaderFactory $uploaderFactory,
        JsonFactory $jsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    ) {
        $this->_test = $test;
        $this->uploaderFactory = $uploaderFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $jsonFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }
    public function execute()
    {
        $response = [];
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        $test = $this->_test->create();
        $test->setData($data);
        if($test->save()){
             $resultJson =  $this->resultJsonFactory->create();
             $result = $resultJson->setData("Data Save.");
             return $result;
            // $this->messageManager->addSuccessMessage(__('You saved data.'));
            //  return $response = [
            //     'success' => true,
            //     'data'     => __('Data has been saved.')
            // ];
            $resultPage = $this->resultPageFactory->create();
            $blockHtml = $resultPage->getLayout()
            ->createBlock('Bluethink\Verify\Block\Index\Popup')
            ->setTemplate('Bluethink_Verify::popup.phtml')
            ->toHtml();
            $this->getResponse()->setBody($blockHtml);
        }else{
            $response = [
                'success' => false,
                'msg'     => __('Data does not save.')
            ];
            $this->messageManager->addErrorMessage(__('Data was not saved.'));
        }
        // return $this->resultJsonFactory->create()->setData($response);
        // print_r($msg);
        $resultRedirect->setPath('Verify/index/index');
        return $resultRedirect;
    }
}