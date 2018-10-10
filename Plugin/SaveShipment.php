<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SaveShipment
 * @package Botgento\Base\Plugin
 */
class SaveShipment
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    public $orderRepository;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Botgento\Base\Model\RecipientRepository
     */
    public $recipientRepository;
    /**
     * @var \Botgento\Base\Helper\Data
     */
    private $helper;
    /**
     * @var \Botgento\Base\Model\CronLogRepository
     */
    public $cronLog;

    /**
     * SaveShipment constructor.
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Botgento\Base\Model\RecipientRepository $recipientRepository
     * @param \Botgento\Base\Helper\Data $helper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Botgento\Base\Model\RecipientRepository $recipientRepository,
        \Botgento\Base\Helper\Data $helper,
        ScopeConfigInterface $scopeConfig,
        \Botgento\Base\Model\CronLogRepository $cronLog
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->recipientRepository = $recipientRepository;
        $this->helper = $helper;
        $this->cronLog = $cronLog;
    }

    /**
     * Sends Shipment Notification on customers facebook
     *
     * @param \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $save
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $save,
        $result
    ) {
        if (!$save->getRequest()->getParam('order_id', false)) {
            return $result;
        }
        $helper = $this->helper;
        $orderId = $save->getRequest()->getParam('order_id', false);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        $storeId = $order->getStoreId();
        $websiteId = $helper->getWebsiteId($storeId);
        $status = $this->scopeConfig->getValue(
            $helper->getStatusPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $valid = $this->scopeConfig->getValue(
            $helper->getValidPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $snd_cnf = $this->scopeConfig->getValue(
            $helper->getSendOrderShipPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $hexCode = $this->scopeConfig->getValue(
            $helper->getHexCodePath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if ($status>0 && $snd_cnf>0 && $valid>0) {
            $recipient = $this->recipientRepository->getByCustomerEmail($order->getCustomerEmail());
            $api_token = $this->scopeConfig->getValue(
                $helper->getApiTokenPath(),
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
            $this->curl->addHeader('Authorization', 'Bearer ' . $api_token);
            $elements = null;
            $ship = $order->getShippingAddress();
            $address = implode(', ', array_filter($ship->getStreet())) . ', ';
            $address .= $ship->getCity() . ', ' . $ship->getRegion() . ', ';
            $address .= $ship->getPostcode() . ' ,' . $ship->getCountryId();
            $shipping_carrier = '';
            $tracking_number = '';
            if (!empty($save->getRequest()->getParam('tracking', [])) > 0 && !is_bool($recipient)) {
                $track = $save->getRequest()->getParam('tracking');
                $tracking = end($track);
                $shipping_carrier = $tracking['title'];
                $tracking_number = $tracking['number'];
            }
            /** @var \Botgento\Base\Model\CronLog $cronLog */
            $cronLog = $this->cronLog->getByOrderId($order->getIncrementId());
            if (is_bool($cronLog)) {
                return false;
            }
            $cronLog->setStatus(1);
            $this->cronLog->save($cronLog);
            // default value
            $recipient_id = $recipient->getRecipientId();
            // get value from cron log table
            if (!is_bool($cronLog)) {
                $recipient_id = $cronLog->getRecipientId();
            }
            $billing = $order->getBillingAddress();
            $postData = ['payload' => json_encode([
                'recipient_name' => $billing->getPrefix()
                    . $billing->getFirstname()
                    . ' ' . $billing->getLastname(),
                'order_number' => $order->getIncrementId(),
                'shipping_carrier' => $shipping_carrier,
                'tracking_number' =>   $tracking_number,
                'shipping_address'  => $address]),
                'user_ref' => $recipient_id,
                'email' => $order->getCustomerEmail()
            ];
            $apiurl = $this->helper->getApiUrl($hexCode, 'order-shipment');
            $this->curl->post($apiurl, $postData);
        }
        return $result;
    }
}
