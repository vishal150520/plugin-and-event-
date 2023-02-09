<?php
/**
 * Copyright © Bluethink All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\NewsletterPopup\Controller\Subscriber;

use Exception;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class NewAction extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /* @var JsonFactory /
    protected $resultJsonFactory;

    /**
     * Construct.
     *
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param JsonFactory $jsonFactory
     * @param EmailValidator|null $emailValidator
     * @param CustomerRepositoryInterface|null $customerRepository
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        SubscriptionManagerInterface $subscriptionManager,
        JsonFactory $jsonFactory,
        EmailValidator $emailValidator = null,
        CustomerRepositoryInterface $customerRepository = null
    ) {
        $this->resultJsonFactory = $jsonFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->subscriptionManager = $subscriptionManager;
        $this->emailValidator = $emailValidator ?: ObjectManager::getInstance()->get(EmailValidator::class);
        $this->customerRepository = $customerRepository ?: ObjectManager::getInstance()
            ->get(CustomerRepositoryInterface::class);
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement,
            $subscriptionManager,
            $emailValidator
        );
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @throws LocalizedException
     * @return void
     */
    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->isLoggedIn()
            && ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId))
        ) {
            throw new LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @throws LocalizedException
     * @return void
     */
    protected function validateGuestSubscription()
    {
        if ($this->_objectManager->get(ScopeConfigInterface::class)
                ->getValue(
                    Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
                    ScopeInterface::SCOPE_STORE
                ) != 1
            && !$this->_customerSession->isLoggedIn()
        ) {
            throw new LocalizedException(
                __(
                    'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                    $this->_customerUrl->getRegisterUrl()
                )
            );
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @throws LocalizedException
     * @return void
     */
    protected function validateEmailFormat($email)
    {
        if (!$this->emailValidator->isValid($email)) {
            throw new LocalizedException(__('Please enter a valid email address.'));
        }
    }

    /**
     * Check if customer with provided email exists and return its id
     *
     * @param string $email
     * @param int $websiteId
     * @return int|null
     */
    private function getCustomerId(string $email, int $websiteId): ?int
    {
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
            return (int)$customer->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * After subscription check msg
     *
     * @return Json
     */
    public function execute()
    {
        $response = [];
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');

            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $websiteId  = (int)$this->_storeManager->getStore()->getWebsiteId();
                $subscriber = $this->_subscriberFactory->create()->loadBySubscriberEmail($email, $websiteId);
                if ($subscriber->getId()
                    && (int)$subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED) {
                        $response = [
                            'success' => true,
                            'msg'     => __('This email address is already subscribed.')
                        ];
                        $this->messageManager->addError(__('This email address is already subscribed.'));
                } else {
                    $storeId = (int)$this->_storeManager->getStore()->getId();
                    $currentCustomerId = $this->getCustomerId($email, $websiteId);

                    $subscriber = $currentCustomerId
                        ? $this->subscriptionManager->subscribeCustomer($currentCustomerId, $storeId)
                        : $this->subscriptionManager->subscribe($email, $storeId);
                    $response = [
                        'success' => true,
                        'msg'     => __('Thank you for your subscription.')
                    ];
                    $this->messageManager->addSuccessMessage(__('Thank you for your subscription.'));
                }
            } catch (LocalizedException $e) {
                $response = [
                    'success' => false,
                    'msg'     => $e->getMessage()
                ];
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $response = [
                    'success' => false,
                    'msg'     => $e->getMessage()
                ];
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}