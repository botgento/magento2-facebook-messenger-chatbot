<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Model;

use \Botgento\Base\Helper\Data as Helper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\CcConfig;
use \Magento\Payment\Model\CcGenericConfigProvider;
use \Magento\Store\Model\ScopeInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use \Magento\Checkout\Model\Cart;

/**
 * Class ConfigProvider
 * @package Botgento\Base\Model
 */
class ConfigProvider extends CcGenericConfigProvider
{
    /**
     * @var Cart
     */
    public $cart;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var RecipientRepository
     */
    private $recipientRepo;
    /**
     * @var CustomerSession
     */
    private $session;
    /**
     * @var \Botgento\Base\Helper\Data
     */
    private $helper;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * ConfigProvider constructor.
     * @param CcConfig $ccConfig
     * @param PaymentHelper $paymentHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param RecipientRepository $recipientRepo
     * @param CurrentCustomer $session
     * @param Helper $helper
     * @param UrlInterface $url
     * @param Cart $cart
     * @param array $methodCodes
     */
    public function __construct(
        CcConfig $ccConfig,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        RecipientRepository $recipientRepo,
        CurrentCustomer $session,
        Helper $helper,
        UrlInterface $url,
        Cart $cart,
        array $methodCodes = []
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->recipientRepo    = $recipientRepo;
        $this->session          = $session;
        $this->helper           = $helper;
        $this->url              = $url;
        $this->cart = $cart;
        parent::__construct($ccConfig, $paymentHelper, $methodCodes);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $helper = $this->helper;
        $refId              = null;
        $recipientId        = null;
        $config             = parent::getConfig();
        $websiteId = $helper->getWebsiteId();
        $status = $this->scopeConfig->getValue(
            $this->helper->getStatusPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $fbButton = $this->scopeConfig->getValue(
            $this->helper->getFbCheckboxStatusPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if ($status && $fbButton) {
            $subscribed = 0;
            $pageArr = null;
            if ($this->session->getCustomerId()) {
                $row = $this->recipientRepo->getByCustomerId($this->session->getCustomerId());
                if (!is_bool($row)) {
                    $subscribed = true;
                }
            }
            $pageArr = [
                'page_uri' => $this->url->getCurrentUrl(),
                'quote_id' => $this->cart->getQuote()->getId()
            ];
            $userRef = $this->helper->generateUserRef($pageArr);
            $configAr = [
                'botgento' => [
                    'config' => [
                        'status' => (boolean)$this->scopeConfig->getValue(
                            $helper->getStatusPath(),
                            ScopeInterface::SCOPE_WEBSITE,
                            $websiteId
                        )
                    ]
                ]
            ];
            if ($configAr['botgento']['config']['status'] === true) {
                $configAr['botgento']['config'] = [
                    'status' => true,
                    'send_order_cnf' => $this->scopeConfig->getValue(
                        $helper->getSendOrderCnfPath(),
                        ScopeInterface::SCOPE_WEBSITE,
                        $websiteId
                    ),
                    'send_ship_detail' => $this->scopeConfig->getValue(
                        $helper->getSendOrderShipPath(),
                        ScopeInterface::SCOPE_WEBSITE,
                        $websiteId
                    ),
                    'user_ref' => $userRef,
                    'origin' => $this->url->getBaseUrl(),
                    'app_id' => $this->scopeConfig->getValue(
                        $helper->getAppIdPath(),
                        ScopeInterface::SCOPE_WEBSITE,
                        $websiteId
                    ),
                    'page_id' => $this->scopeConfig->getValue(
                        $helper->getPageIdPath(),
                        ScopeInterface::SCOPE_WEBSITE,
                        $websiteId
                    ),
                    'subscribed' => $subscribed
                ];
            }
            $config = array_merge_recursive($config, $configAr);
        }
        return $config;
    }
}
