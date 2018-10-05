<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Observer;

use \Magento\Framework\Event\ObserverInterface;

/**
 * Class PlaceOrder
 * @package Botgento\Base\Observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoadCustomerQuoteBefore implements ObserverInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Botgento\Base\Model\SubscriberMappingFactory
     */
    public $subscriberMappingFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory
     */
    public $subscriberMappingCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * PlaceOrder constructor.
     * @param \Botgento\Base\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory
     * @param \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory
     */
    public function __construct(
        \Botgento\Base\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory,
        \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory
    ) {
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->_storeManager = $storeManager;
        $this->quoteFactory = $quoteFactory;
        $this->subscriberMappingFactory = $subscriberMappingFactory;
        $this->subscriberMappingCollectionFactory = $subscriberMappingCollectionFactory;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnable = $this->helper->getModuleIsEnableAndValid();

        if ($moduleEnable) {
            $quoteId = $observer->getEvent()->getCheckoutSession()->getQuoteId();

            try {
                $customerQuote = $this->quoteRepository->getForCustomer($this->_customerSession->getCustomerId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $customerQuote = $this->quoteFactory->create();
            }

            $customerQuote->setStoreId($this->_storeManager->getStore()->getId());

            if ($customerQuote->getId() && $quoteId != $customerQuote->getId()) {
                $uuid = $this->helper->getUuid();

                $collection = $this->subscriberMappingCollectionFactory->create()
                    ->addFieldToFilter("quote_id", $quoteId)
                    ->getLastItem();

                $custQuoteSubsrcibeMappingCol = $this->subscriberMappingCollectionFactory->create()
                    ->addFieldToFilter("quote_id", $customerQuote->getId())
                    ->addFieldToFilter("uuid", $uuid)
                    ->getLastItem();

                if ($custQuoteSubsrcibeMappingCol->hasData()) {
                    $subscriberMappingModel = $this->subscriberMappingFactory->create()->load($collection->getId());
                    $subscriberMappingModel->delete();
                } else {
                    if ($collection->getId()) {
                        $subscriberMappingModel = $this->subscriberMappingFactory->create()->load($collection->getId());
                        $subscriberMappingModel->setQuoteId($customerQuote->getId());
                        $subscriberMappingModel->save();
                    }
                }
            }
        }
    }
}
