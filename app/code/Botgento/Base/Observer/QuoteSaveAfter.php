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
class QuoteSaveAfter implements ObserverInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $helper;

    /**
     * @var \Botgento\Base\Model\SubscriberMappingFactory
     */
    public $subscriberMappingFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory
     */
    public $subscriberMappingCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * PlaceOrder constructor.
     * @param \Botgento\Base\Helper\Data $helper
     * @param \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory
     * @param \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(
        \Botgento\Base\Helper\Data $helper,
        \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory,
        \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->helper = $helper;
        $this->subscriberMappingFactory = $subscriberMappingFactory;
        $this->subscriberMappingCollectionFactory = $subscriberMappingCollectionFactory;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnable = $this->helper->getModuleIsEnableAndValid();

        if ($moduleEnable) {
            $quote = $observer->getQuote();
            if ($quote->getId()) {
                $quoteId = $quote->getId();

                $uuid = $this->helper->getUuid();
                $collection = $this->subscriberMappingCollectionFactory->create()
                    ->addFieldToFilter('quote_id', $quoteId)
                    ->addFieldToFilter("uuid", $uuid);

                if (!$collection->getSize()) {
                    $subscriberMappingModel = $this->subscriberMappingFactory->create();
                    $subscriberMappingModel->setQuoteId($quoteId);
                    $subscriberMappingModel->setUuid($uuid);

                    $cookie = $this->cookieManager->getCookie(\Botgento\Base\Helper\Data::BGC_OPTION_COOKIE_NAME);
                    if (!empty($cookie) && $cookie == 1) {
                        $subscriberMappingModel->setIsButtonPress(1);
                    } else {
                        $subscriberMappingCollection = $this->subscriberMappingCollectionFactory->create()
                            ->addFieldToFilter("uuid", $uuid)
                            ->getLastItem();

                        if ($subscriberMappingCollection->hasData()) {
                            $subscriberMappingModel->setIsButtonPress($subscriberMappingCollection->getIsButtonPress());
                        } else {
                            $subscriberMappingModel->setIsButtonPress(0);
                        }
                    }

                    $subscriberMappingModel->save();
                }
            }
        }
    }
}
