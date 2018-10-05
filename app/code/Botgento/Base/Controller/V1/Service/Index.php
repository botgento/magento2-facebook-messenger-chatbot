<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Controller\V1\Service;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Setup\Exception;

/**
 * Class Index
 *
 * @package Botgento\Base\Controller\V1\Service
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Botgento\Base\Helper\Data
     */
    private $helper;

    /**
     * @var \Botgento\Base\Helper\Api
     */
    public $apiHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Botgento\Base\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Botgento\Base\Helper\Data $helper,
        \Botgento\Base\Helper\Api $apiHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->apiHelper = $apiHelper;

        parent::__construct($context);
    }

    /**
     * Api action
     *
     * @return $this
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = [];
        try {
            $payLoad = $this->getRequest()->getParam('payload', '');
            $payLoad = json_decode($payLoad, true);
            if (is_array($payLoad)) {
                try {
                    // Set params in session
                    $this->getRequest()->setParams($payLoad);

                    if (isset($payLoad['options'])) {
                        $options = $payLoad['options'];

                        if (is_array($options) && array_key_exists("type", $options)) {
                            unset($options['type']);
                        }
                    }

                    if (isset($options)) {
                        $this->getRequest()->setParams($options);
                    }
                    $type = $this->getRequest()->getParam('type', '');
                    $limit = $this->getRequest()->getParam('limit', 20);
                    $offset = $this->getRequest()->getParam('offset', 0);

                    $limit = ((int)$limit > 0 ? $limit : 20);
                    $offset = ((int)$offset > 0 ? $offset : 0);

                    $helper = $this->helper;
                    if ($type == 'categories.list') {
                        $data = $helper->getCategoryList($offset, $limit);
                    } elseif ($type == 'category.detail') {
                        $catId = $this->getRequest()->getParam('catId', false);
                        if ($catId) {
                            $data = $helper->getCategoryDetail($offset, $limit, $catId);
                        }
                    } elseif ($type == 'products.list') {
                        $catId = $this->getRequest()->getParam('catId', false);
                        $data = $helper->getProductList($offset, $limit, $catId);
                    } elseif ($type == 'product.detail') {
                        $prodSkus = $this->getRequest()->getParam('prodSkus', false);
                        if ($prodSkus) {
                            $data = $helper->getProductDetail($offset, $limit, $prodSkus);
                        }
                    } elseif ($type == 'catalog.details') {
                        $parentCatId = $this->getRequest()->getParam('parentCatId', false);
                        if (!is_bool($parentCatId) && is_numeric($parentCatId)) {
                            $data = $helper->getCatalogDetails($offset, $limit, $parentCatId);
                        }
                    } elseif ($type == 'order.lists') {
                        $email = $this->getRequest()->getParam('customer_email', false);
                        if (!is_bool($email)) {
                            $data = $helper->getOrderLists($email);
                        }
                    } elseif ($type == 'order.detail') {
                        $orderNo = $this->getRequest()->getParam('order_number', false);
                        if (!is_bool($orderNo)) {
                            $data = $helper->getOrderDetail($orderNo);
                        }
                    } elseif ($type == 'wishlist.items') {
                        $email = $this->getRequest()->getParam('customer_email', false);
                        if (!is_bool($email)) {
                            $data = $helper->getWishlistItems($offset, $limit, $email);
                        }
                    } elseif ($type == 'fb.get-message-button') {
                        $page = $this->getRequest()->getParam('page');
                        $data = $helper->getFbMessageButton($page);
                    } elseif ($type == 'ping') {
                        $data = $helper->getPing();
                    } elseif ($type == 'config.details.save') {
                        $header = $this->getRequest()->getHeader('Authorization');
                        if (!empty($header)) {
                            $data = $helper->getConfigDetailsSave($payLoad['options'], $header);
                        } else {
                            $data['status'] = 'error';
                            $data['error'] = 'Authorization Failed';
                        }
                    } elseif ($type == 'purge.data') {
                        $data = $helper->purgeBotData();
                    } elseif ($type == 'data') {
                        $options = $this->getRequest()->getParam('options');
                        $data = $this->apiHelper->setOptions($options)->getData();
                    } elseif ($type == 'store.sync.attributes') {
                        $options = $this->getRequest()->getParam('options');
                        $data = $helper->saveAttributeDataToTable($options);
                    } elseif ($type == 'quote.orders') {
                        $options = $this->getRequest()->getParam('options');
                        $data = $helper->getOrderStatusFromQuote($options);
                    } else {
                        $data['status'] = 'error';
                        $data['error'] = 'Invalid Api requests';
                    }
                    /**
                     * elseif ($type == 'category.layered.navigation') {
                     * $data = $helper->getLayerNavigation($offset + 1, $limit);
                     * }*/
                } catch (Exception $e) {
                    $data['status'] = 'error';
                    $data['error'] = $e->getMessage();
                }
            } else {
                $data['status'] = 'error';
                $data['error'] = 'Invalid Api requests';
            }
        } catch (\Exception $e) {
            $data['status'] = 'error';
            $data['error'] = $e->getMessage();
        }

        $result->setHeader('Content-Type', 'application/json');
        return $result->setData($data);
    }
}
