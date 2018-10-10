<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Controller\Demo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 *
 * @package Botgento\Base\Controller\Demo
 */
class Bgc extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;

    /**
     * @var \Botgento\Base\Model\SubscriberMappingFactory
     */
    public $subscriberMappingFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory
     */
    public $subscriberMappingCollectionFactory;

    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $helper;

    /**
     * Bgc constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory
     * @param \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory
     * @param \Botgento\Base\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Botgento\Base\Model\SubscriberMappingFactory $subscriberMappingFactory,
        \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory,
        \Botgento\Base\Helper\Data $helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->subscriberMappingFactory = $subscriberMappingFactory;
        $this->subscriberMappingCollectionFactory = $subscriberMappingCollectionFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return $this
     */
    public function execute()
    {
        $authId = $this->getRequest()->getHeader('Authorization');
        $auth = $this->helper->getApiToken();
        if (!empty($auth) && $auth == $authId) {
            $quoteId = $this->checkoutSession->getQuoteId();
            $uuid = $this->helper->getUuid();
            $collection = $this->subscriberMappingCollectionFactory->create()
                ->addFieldToFilter('quote_id', $quoteId)
                ->addFieldToFilter('uuid', $uuid)
                ->getLastItem();

            if ($collection->getId()) {
                $subscriberMappingModel = $this->subscriberMappingFactory->create()->load($collection->getId());
                $subscriberMappingModel->setIsButtonPress(1);
                $subscriberMappingModel->save();
            }
            $message =  "Success";
        } else {
            $message = "Failed";
        }
        return $this->getResponse()->setBody($message);
    }
}
