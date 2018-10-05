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
 * Class SendCookieDataToBotgento
 * @package Botgento\Base\Observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendCookieDataToBotgento implements ObserverInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * SendCookieDataToBotgento constructor.
     * @param \Botgento\Base\Helper\Data $helper
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(
        \Botgento\Base\Helper\Data $helper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
    ) {
        $this->helper = $helper;
        $this->curl = $curl;
        $this->localeCurrency = $localeCurrency;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnable = $this->helper->getModuleIsEnableAndValid();

        if ($moduleEnable) {
            $event = $observer->getEvent();
            $order = $event->getOrder();

            $bgUtmCookies = [];
            foreach ($_COOKIE as $key => $value) {
                if (stripos($key, 'bg_utm') === 0) {
                    $bgUtmCookies[$key] = $value;
                }
            }

            if (!empty($bgUtmCookies)) {
                $token = $this->helper->getApiToken();
                $websiteHash = $this->helper->getWebsiteHash();

                $url = $this->helper->getApiUrl($websiteHash, 'conversion');

                $data = [];
                $data['type'] = 'abandoned-cart';
                $data['customer_email'] = $order->getCustomerEmail();
                $data['order_id'] = $order->getIncrementId();
                $data['order_amount'] = $order->getBaseGrandTotal();
                $data['subtotal'] = $order->getBaseSubtotal();
                $data['order_currency_code'] = $order->getBaseCurrencyCode();
                $data['order_currency_symbol'] = $this->localeCurrency->getCurrency($order->getBaseCurrencyCode())
                    ->getSymbol();
                $data['cookies'] = $bgUtmCookies;

                $jsonData = ['conversion_data' => json_encode($data)];

                $curl = $this->curl;

                $curl->addHeader('Authorization', "Bearer ".$token);

                $curl->post($url, $jsonData);

                $response = $curl->getBody();

                $result = json_decode($response, true);

                if (is_array($result) && $result['status'] == 'success') {
                    foreach ($bgUtmCookies as $cookieName => $cookieValue) {
                        $this->cookieManager->deleteCookie($cookieName);
                    }
                }
            }
        }
    }
}
