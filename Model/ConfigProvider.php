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
use \Magento\Customer\Model\Session\Proxy as CustomerSession;

/**
 * Class ConfigProvider
 * @package Botgento\Base\Model
 */
class ConfigProvider extends CcGenericConfigProvider
{
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
     * @param ScopeConfigInterface $scopeConfig
     * @param RecipientRepository $recipientRepo
     * @param CustomerSession $session
     * @param Helper $helper
     * @param UrlInterface $url
     */
    public function __construct(
        CcConfig $ccConfig,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        RecipientRepository $recipientRepo,
        CustomerSession $session,
        Helper $helper,
        UrlInterface $url,
        array $methodCodes = []
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->recipientRepo    = $recipientRepo;
        $this->session          = $session;
        $this->helper           = $helper;
        $this->url              = $url;
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
            if ($this->session->isLoggedIn()) {
                $row = $this->recipientRepo->getByCustomerId($this->session->getCustomer()->getId());
                if (!is_bool($row)) {
                    $refId = rand(100000, 999999);
                    $recipientId = $row->getRecipientId();
                    $subscribed = 1;
                } else {
                    $refId = rand(100000, 999999);
                    $recipientId = $refId;
                }
            } else {
                $refId = rand(100000, 999999);
                $recipientId = $refId;
            }
            $myConfig = [
                'botgento' => [
                    'config' => [
                        'status' => (boolean)$this->scopeConfig->getValue(
                            $helper->getStatusPath(),
                            ScopeInterface::SCOPE_WEBSITE,
                            $websiteId
                        ),
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
                        'ref_id' => $refId,
                        'recipientId' => $recipientId,
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
                    ]
                ]
            ];
            $config = array_merge_recursive($config, $myConfig);
        }
        return $config;
    }
}
