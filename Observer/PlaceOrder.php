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

use \Botgento\Base\Helper\Data as Helper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Framework\UrlInterface;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Helper\ImageFactory;
use \Magento\SalesRule\Model\CouponFactory;
use \Magento\SalesRule\Model\RuleFactory;
use \Magento\CatalogRule\Model\Rule;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Framework\HTTP\Client\Curl;

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
class PlaceOrder implements ObserverInterface
{
    /**
     *
     */
    private $cardTypeTranslationMap = [
        'checkmo' => 'Check / Money order',
        'cod' => 'Cash On Delivery',
        'AE' => 'American Express',
        'DI' => 'Discover',
        'DC' => 'Diners Club',
        'JCB' => 'JCB',
        'MC' => 'MasterCard',
        'VI' => 'Visa',
    ];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var UrlInterface
     */
    private $u;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $img;
    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    private $coupon;
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    private $salesRule;
    /**
     * @var \Magento\CatalogRule\Model\Rule
     */
    private $catRule;
    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule
     */
    private $catRulesRes;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;
    /**
     * @var StoreManagerInterface
     */
    private $store;
    /**
     * @var \Botgento\Base\Model\CronLogRepository
     */
    public $cronLogRepository;
    /**
     * @var \Botgento\Base\Model\CronLogFactory
     */
    public $cronLogFactory;
    /**
     * @var \Botgento\Base\Model\RecipientRepository
     */
    public $recipientRepository;
    /**
     * @var \Botgento\Base\Model\RecipientFactory
     */
    public $recipientFactory;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var \Botgento\Base\Helper\Data
     */
    private $helper;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $countryFactory;

    /**
     * PlaceOrder constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlBuilder
     * @param ImageFactory $productImageHelper
     * @param CouponFactory $coupon
     * @param RuleFactory $salesRule
     * @param Rule $catRule
     * @param \Magento\CatalogRule\Model\ResourceModel\RuleFactory $catRuleRes
     * @param TimezoneInterface $localeDate
     * @param Curl $curl
     * @param DateTime $dateTime
     * @param \Botgento\Base\Model\CronLogRepository $cronLogRepository
     * @param \Botgento\Base\Model\CronLogFactory $cronLogFactory
     * @param \Botgento\Base\Model\RecipientRepository $recipientRepository
     * @param \Botgento\Base\Model\RecipientFactory $recipientFactory
     * @param Helper $helper
     * @param StoreManagerInterface $store
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        ImageFactory $productImageHelper,
        CouponFactory $coupon,
        RuleFactory $salesRule,
        Rule $catRule,
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $catRuleRes,
        TimezoneInterface $localeDate,
        Curl $curl,
        DateTime $dateTime,
        \Botgento\Base\Model\CronLogRepository $cronLogRepository,
        \Botgento\Base\Model\CronLogFactory $cronLogFactory,
        \Botgento\Base\Model\RecipientRepository $recipientRepository,
        \Botgento\Base\Model\RecipientFactory $recipientFactory,
        Helper $helper,
        StoreManagerInterface $store,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
    
        $this->scopeConfig = $scopeConfig;
        $this->u = $urlBuilder;
        $this->img = $productImageHelper->create();
        $this->coupon = $coupon->create();
        $this->salesRule = $salesRule->create();
        $this->catRule = $catRule;
        $this->catRulesRes = $catRuleRes->create();
        $this->localeDate = $localeDate;
        $this->store = $store;
        $this->cronLogRepository = $cronLogRepository;
        $this->cronLogFactory = $cronLogFactory;
        $this->recipientRepository = $recipientRepository;
        $this->recipientFactory = $recipientFactory;
        $this->curl = $curl;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->helper;
        $websiteId = $this->store->getStore()->getWebsiteId();
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
            $helper->getSendOrderCnfPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $snd_cnf_afr = $this->scopeConfig->getValue(
            $helper->getSendOrderCnfAfterPath(),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        // check status
        if ($status && $snd_cnf && $valid) {
            $req = json_decode(file_get_contents("php://input"), true);
            $api_token = $this->scopeConfig->getValue(
                $helper->getApiTokenPath(),
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
            if (!isset($req['fbState'])) {
                return;
            }
            if ($req['fbState'] != 'checked') {
                return;
            }
            if (!isset($req['recipientId'])) {
                return;
            }
            /** @var \Magento\Quote\Model\QuoteManagement $event */
            $event = $observer->getEvent();
            /** @var \Magento\Sales\Model\Order $order */
            $order = $event->getOrder();
            $itemArray = [];

            foreach ($order->getAllVisibleItems() as $item) {
                $price = $item->getPrice();
                $prdTitle = $item->getProduct()->getName();
                /** @var \Magento\Catalog\Model\Product $prd */
                $product = $this->productRepository->getById($item->getProductId());
                $itemArray[] = [
                    'title' => substr($prdTitle, 0, 250),
                    'subtitle' => !empty($product->getDescription())
                        ? substr(
                            explode("\n", strip_tags($product->getDescription()))[0],
                            0,
                            250
                        )
                        : $product->getName(),
                    'quantity' => abs($item->getQtyOrdered()),
                    'price' => $price,
                    'currency' => 'USD',
                    'image_url' => $this->helper->getImageFromData($product, 'product', 'image_url', 'order_image')
                ];
            }

            /** Adjustments data array */
            $adjustments = [];
            $storeId = $order->getStoreId();
            $websiteId = $this->store->getStore($storeId)->getWebsiteId();
            $customerGroupId = $order->getCustomerGroupId();
            $orderDate = $this->localeDate->date($order->getCreatedAt())->format('m/d/y H:i:s');
            $ordTimeStamp = strtotime($orderDate);

            $couponCode = $order->getCouponCode();
            $ruleIndex = 0;
            if ($couponCode) {
                $salesRuleId = $this->coupon->loadByCode($couponCode)->getRuleId();
                $salesRule = $this->salesRule->load($salesRuleId);
                $adjustments[] = [
                    'name' => $salesRule->getName(),
                    'amount' => abs($order->getDiscountAmount())];
                ++$ruleIndex;
            }

            $oldRuleName = '';
            foreach ($order->getAllItems() as $item) {
                $productId = $item->getProductId();
                $isValid = $this->catRulesRes
                    ->getRulesFromProduct($ordTimeStamp, $websiteId, $customerGroupId, $productId);
                if (!empty($isValid)) {
                    $ruleItem = $this->catRule->load($isValid[0]['rule_id']);
                    if ($oldRuleName != $ruleItem->gdetName()) {
                        $oldRuleName = $ruleItem->getName();
                    } elseif ($oldRuleName == $ruleItem->getName()) {
                        continue;
                    }

                    $adjustments[] = ['name' => $ruleItem->getName(), 'amount' => $ruleItem->getDiscountAmount()];
                    ++$ruleIndex;
                }
            }
            $method = $order->getPayment()->getMethod();
            if (empty($method)) {
                $method = $this->cardTypeTranslationMap[$order->getPayment()->getCcType()] . ' ';
                $method .= $order->getPayment()->getCcLast4();
            } else {
                $method = $this->cardTypeTranslationMap[$method];
            }
            $billing = $order->getBillingAddress();
            $countryName = $this->countryFactory->create()->loadByCode($billing->getCountryId())->getName();
            $region = trim($billing->getRegion()) ? trim($billing->getRegion()) : trim($billing->getRegionCode());
            $region = !empty($region) ? $region : $countryName;
            $payLoad = [
                'template_type' => 'receipt',
                'recipient_name' => $billing->getPrefix() .
                    $billing->getFirstname() . ' ' .
                    $billing->getLastname(),
                'order_number' => $order->getIncrementId(),
                'currency' => $order->getOrderCurrencyCode(),
                'payment_method' => $method,
                'order_url' => $this->u->getUrl('sales/order') . 'view/order_id/' . $order->getId(),
                'timestamp' => strtotime($order->getCreatedAt()),
                'address' => [
                    'street_1' => $order->getBillingAddress()->getStreetLine(1),
                    'street_2' => $order->getBillingAddress()->getStreetLine(2),
                    'city' => $order->getBillingAddress()->getCity(),
                    'postal_code' => $order->getBillingAddress()->getPostcode(),
                    'state' => $region,
                    'country' => $countryName
                ],
                'summary' => [
                    'subtotal' => $order->getSubtotal(),
                    'shipping_cost' => $order->getShippingAmount(),
                    'total_tax' => $order->getTaxAmount(),
                    'total_cost' => $order->getGrandTotal()
                ],
                'elements' => $itemArray
            ];
            if (!empty($adjustments)) {
                $payLoad['adjustments'] = $adjustments;
            }
            $recipientObj = $this->recipientRepository->getByCustomerEmail($order->getCustomerEmail());
            /** Prepare curl request */
            $message = '';
            $hexCode = $this->scopeConfig->getValue(
                $helper->getHexCodePath(),
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
            $recipient_id = $req['recipientId'];
            $isPrimary = !$order->getCustomerIsGuest();
            //already subscribed
            $postData = ["payload" => __('Thank You for your order.'),
                "email" => $order->getCustomerEmail(),
                "quote_id" => $order->getQuoteId(),
                'is_primary' => (int)$isPrimary,
                "user_ref" => $req['recipientId']];
            $this->curl->addHeader('Authorization', 'Bearer ' . $api_token);

            $apiurl = $this->helper->getApiUrl($hexCode, 'checkbox-checked');
            $this->curl->post($apiurl, $postData);

            if ($this->curl->getBody()) {
                $data = json_decode($this->curl->getBody(), true);
                if (isset($data['data']['recepient_id'])) {
                    $recipient_id = $data['data']['recepient_id'];
                }
            }
            $payLoadData['payload'] = json_encode($payLoad);
            $payLoadData['user_ref'] = $req['recipientId'];
            $payLoadData['email'] = $order->getCustomerEmail();
            $cronLog = $this->cronLogFactory->create();
            /** @var /Botgento/Base/Model/CronLog $cronLog */
            $cronLog->setRecipientId($recipient_id);
            $cronLog->setApiData(json_encode($payLoadData));
            $cronLog->setEmail($order->getCustomerEmail());
            $cronLog->setIsGuest($order->getCustomerIsGuest());
            $cronLog->setOrderId($order->getIncrementId());
            $cronLog->setWebsiteId($websiteId);
            $cronLog->setStatus(0);
            $cronLog->setCreatedAt($this->dateTime->gmtTimestamp());
            $snd_cnf_afr = (int)$snd_cnf_afr + 60;
            $date = new \DateTime('now');
            $date->add(new \DateInterval('PT' . $snd_cnf_afr . 'S'));
            $cronLog->setSendTime($date->getTimestamp());
            $this->cronLogRepository->save($cronLog);

            if (is_bool($recipientObj)) {
                $recipientObj = $this->recipientFactory->create();
                $recipientObj->setRecipientId($recipient_id);
                $recipientObj->setCustomerId($order->getCustomerId());
                $recipientObj->setCustomerEmail($order->getCustomerEmail());
                $this->recipientRepository->save($recipientObj);
            } else {
                $recipientObj->setRecipientId($recipient_id);
                $recipientObj->setCustomerId($order->getCustomerId());
                $this->recipientRepository->save($recipientObj);
            }
        }
    }
}
