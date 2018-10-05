<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Controller\Instock;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 *
 * @package Botgento\Base\Controller\Demo
 */
class Alert extends Action
{
    /**
     * @var \Botgento\Base\Model\InStockAlertFactory
     */
    public $inStockAlertFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory
     */
    public $inStockAlertCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * Bgc constructor.
     * @param Context $context
     * @param \Botgento\Base\Model\InStockAlertFactory $inStockAlertFactory
     * @param \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory $inStockAlertCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        Context $context,
        \Botgento\Base\Model\InStockAlertFactory $inStockAlertFactory,
        \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory $inStockAlertCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->inStockAlertFactory = $inStockAlertFactory;
        $this->inStockAlertCollectionFactory = $inStockAlertCollectionFactory;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return $this
     */
    public function execute()
    {

        $productId = $this->getRequest()->getParam('product_id');

        $uuid = $this->getRequest()->getParam('uuid');

        if (!empty($productId) && !empty($uuid)) {
            $collection = $this->inStockAlertCollectionFactory->create()
                ->addFieldToFilter("product_id", $productId)
                ->addFieldToFilter("uuid", $uuid)
                ->addFieldToFilter("is_notification_sent", 0);

            if (!$collection->getSize()) {
                $websiteId = $this->storeManager->getStore()->getWebsiteId();

                $inStockAlertModel = $this->inStockAlertFactory->create();
                $inStockAlertModel->setUuid($uuid);
                $inStockAlertModel->setProductId($productId);
                $inStockAlertModel->setWebsiteId($websiteId);
                $inStockAlertModel->setIsNotificationSent(0);
                $inStockAlertModel->setCreatedAt($this->dateTime->gmtTimestamp());
                $inStockAlertModel->setUpdatedAt($this->dateTime->gmtTimestamp());
                $inStockAlertModel->save();

                $message = "Subscribed successfully";
            } else {
                $message = "You have allready subscribed for this";
            }
            return $this->getResponse()->setBody($message);
        }
    }
}
