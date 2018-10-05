<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Helper;

use Botgento\Base\Model\CronLogRepository;
use Botgento\Base\Model\RecipientRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;
use \Magento\SalesRule\Model\CouponFactory;
use \Magento\SalesRule\Model\RuleFactory;
use \Magento\CatalogRule\Model\Rule;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Data
 * @package Botgento\Base\Helper
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(Generic.Metrics.CyclomaticComplexity.TooHigh)
 */
class Data extends AbstractHelper
{
    const STATUS = 'botgento_base/base/status';
    const VALID = 'botgento_base/base/valid';

    const API_TOKEN = 'botgento_base/base/api_token';
    const API_URL   = 'https://api.botgento.com/fb/{{HEX_CODE}}/get-messageus-btn';
    const HEX_CODE  = 'botgento_base/base/hex_code';
    const APP_ID    = 'botgento_base/base/app_id';
    const PAGE_ID   = 'botgento_base/base/page_id';
    const VERIFY_URL = 'https://api.botgento.com/verifywebtoken/';

    const API_HTTP  = 'https';
    const API_HOST  = 'api.botgento.com';
    const API_PATH  = '/fb/';

    const SND_ORDER_CNF = 'botgento_base/order/snd_ord_confirmation';
    const SND_ORDER_CNF_AFTER = 'botgento_base/order/snd_order_cnf_seconds';
    const SND_SHIP_CNF = 'botgento_base/order/snd_shipment_detail';

    const FB_CHECKBOX = 'botgento_base/base/checkbox_enable';
    const FB_BUTTON = 'botgento_base/base/facebook_button';

    const INSTOCK_ENABLE = 'botgento_base/instock/enabled';
    const INSTOCK_BUTTON_TEXT = 'botgento_base/instock/button_text';
    const INSTOCK_BUTTON_COLOR = 'botgento_base/instock/button_color';
    const INSTOCK_BUTTON_SIZE = 'botgento_base/instock/button_size';

    const IMG_WIDTH = 600;
    const IMG_HEIGHT = 315;

    const ORDER_IMG_WIDTH = 265;
    const ORDER_IMG_HEIGHT = 265;

    const THUMB_IMG_WIDTH = 80;
    const THUMB_IMG_HEIGHT = 80;

    const BGC_UUID_COOKIE_NAME = 'bgc_uuid';
    const BGC_OPTION_COOKIE_NAME = 'bgc_optin';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    public $categoryCollection;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    private $outputHelper;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $fileSystem;
    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    private $config;
    /**
     * @var \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory
     */
    private $symbolSystemFactory;
    /**
     * @var \Magento\Customer\Model\Session\Proxy
     */
    private $customer;
    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var \Magento\Framework\Api\Filter
     */
    private $filter;
    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    private $filterGroup;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $criteria;
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetSource;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    public $orderRepo;
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
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    public $wishlist;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    public $customerItem;
    /**
     * @var \Magento\Framework\Api\SortOrder
     */
    private $sortOrder;
    /**
     * @var RecipientRepository
     */
    private $fbRecipent;
    /**
     * @var CronLogRepository
     */
    private $cronData;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @var  \Botgento\Base\Model\SyncAttributesFactory
     */
    protected $syncAttributesFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Botgento\Base\Model\SyncLogFactory
     */
    protected $syncLogFactory;
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;
    /**
     * @var \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory
     */
    public $subscriberMappingCollectionFactory;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $productImageHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory
     */
    public $inStockAlertCollectionFactory;

    /**
     * @var \Magento\Framework\View\Page\Title
     */
    public $pageTitle;

    /**
     * @var int
     */
    public $websiteId = 0;
    /**
     * @var int
     */
    public $storeId = 0;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Customer\Model\Session\Proxy $customer
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param Curl $curl
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\View\Asset\Repository $_assetSource
     * @param \Magento\Sales\Model\OrderRepository $orderRepo
     * @param CouponFactory $coupon
     * @param RuleFactory $salesRule
     * @param Rule $catRule
     * @param \Magento\CatalogRule\Model\ResourceModel\RuleFactory $catRuleRes
     * @param DateTime $dateTime
     * @param TimezoneInterface $dateT
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param \Magento\Customer\Model\Customer @customerItem
     * @param \Magento\Framework\Api\SortOrder $sortOrder
     * @param CronLogRepository $cronLogRepository
     * @param RecipientRepository $recipientRepository
     * @param \Botgento\Base\Model\SyncAttributesFactory $syncAttributesFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Botgento\Base\Model\SyncLogFactory $syncLogFactory
     * @param \Magento\Catalog\Helper\Image $productImageHelper
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory $inStockAlertCollectionFactory
     * @param \Magento\Framework\View\Page\Title $pageTitle
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Customer\Model\Session\Proxy $customer,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        Curl $curl,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\View\Asset\Repository $_assetSource,
        \Magento\Sales\Model\OrderRepository $orderRepo,
        CouponFactory $coupon,
        RuleFactory $salesRule,
        Rule $catRule,
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $catRuleRes,
        TimezoneInterface $localeDate,
        DateTime $dateTime,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Customer\Model\Customer $customerItem,
        \Magento\Framework\Api\SortOrder $sortOrder,
        CronLogRepository $cronLogRepository,
        RecipientRepository $recipientRepository,
        \Botgento\Base\Model\SyncAttributesFactory $syncAttributesFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Botgento\Base\Model\SyncLogFactory $syncLogFactory,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Botgento\Base\Model\ResourceModel\SubscriberMapping\CollectionFactory $subscriberMappingCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Botgento\Base\Model\ResourceModel\InStockAlert\CollectionFactory $inStockAlertCollectionFactory,
        \Magento\Framework\View\Page\Title $pageTitle
    ) {
        $areaCode = $appState->getAreaCode();
        if (empty($areaCode)) {
            $appState->setAreaCode('frontend');
        }
        parent::__construct($context);
        $this->categoryCollection = $categoryCollection;
        $this->productRepository = $productRepository;
        $this->symbolSystemFactory = $symbolSystemFactory;
        $this->outputHelper = $outputHelper;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->fileSystem = $fileSystem;
        $this->scopeConfig = $context->getScopeConfig();
        $this->config = $config;
        $this->customer = $customer;
        $this->curl = $curl;
        $this->filter = $filter;
        $this->filterGroup = $filterGroup;
        $this->criteria = $criteria;
        $this->configWriter = $configWriter;
        $this->assetSource = $_assetSource;
        $this->orderRepo = $orderRepo;
        $this->coupon = $coupon->create();
        $this->salesRule = $salesRule->create();
        $this->catRule = $catRule;
        $this->catRulesRes = $catRuleRes->create();
        $this->localeDate = $localeDate;
        $this->dateTime = $dateTime;
        $this->wishlist = $wishlist;
        $this->customerItem = $customerItem;
        $this->sortOrder = $sortOrder;
        $this->fbRecipent = $recipientRepository;
        $this->cronData = $cronLogRepository;
        $this->syncAttributesFactory = $syncAttributesFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->quoteFactory = $quoteFactory;
        $this->syncLogFactory = $syncLogFactory;
        $this->productImageHelper = $productImageHelper;
        $this->localeCurrency = $localeCurrency;
        $this->directoryHelper = $directoryHelper;
        $this->subscriberMappingCollectionFactory = $subscriberMappingCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->inStockAlertCollectionFactory = $inStockAlertCollectionFactory;
        $this->pageTitle = $pageTitle;
    }

    /**
     * To get core config values using path
     *
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Gets category lists
     *
     * @param $offset
     * @param $limit
     * @param null $categoryIds
     * @param bool $refApi
     * @return mixed
     */
    public function getCategoryList($offset, $limit, $categoryIds = null, $refApi = false)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        $collection = $this->categoryCollection->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('description')
            ->addAttributeToFilter('shop_now', 1)
            ->addAttributeToSelect('is_anchor')
            ->setProductStoreId($this->getStoreId())
            ->addIsActiveFilter();

        $isValid = false;
        // Default values
        $data['categories']['list'] = [];
        $data['categories']['item_count'] = 0;
        $data['categories']['total'] = 0;
        $data['categories']['page'] = 1;
        if ($refApi == 'catalog.details') {
            $data['products_in_current_category'] = 0;
        }
        $data['status'] = 'success';
        if ($refApi == 'catalog.details') {
            if ($categoryIds == 0) {
                if ($collection->getSize() < 1) {
                    return $data;
                } else {
                    $category = $this->categoryCollection->create()
                        ->addRootLevelFilter();
                    $_categoryIds = $category->getFirstItem()->getId();
                    $collection
                        ->addFieldToFilter('parent_id', ['eq' => $_categoryIds]);
                }
                $isValid = true;
            } else {
                $collection
                    ->addFieldToFilter('parent_id', ['eq' => $categoryIds]);
                $isValid = true;
                if ($collection->getSize() < 1) {
                    return $data;
                }
            }
        }

        if ($refApi == 'category.detail') {
            if (is_array($categoryIds) && isset($categoryIds)) {
                $categoryIdi = implode(',', $categoryIds);
                if ($categoryIdi !== "") {
                    if ($categoryIdi === "0") {
                        $collection
                            ->addPathFilter('^[0-9]\/[0-9]\/[0-9]*$');
                        $isValid = true;
                    } else {
                        $collection->addFieldToFilter('entity_id', ['in' => $categoryIds]);
                        $isValid = true;
                    }
                }
            }
            if ($collection->getSize() < 1) {
                return $data;
            }
        }
        if (empty($refApi)) {
            $isValid = true;
        }
        $page = ($offset / $limit) + 1;
        $collection->setCurPage($page)
            ->setPageSize($limit);
        $collection->clear();
        $collection->load();

        $list = [];

        // Check for same page number
        if ($isValid && $collection->getSize() && $offset <= $collection->getSize()) {
            $prdCount = 0;
            $parentCategory = null;
            foreach ($collection as $item) {
                /** @var \Magento\Catalog\Model\Category $item */
                $imageArr = $this->getImageFromData($item, 'category');
                $itemArray = [
                    'category_id' => (int)$item->getId(),
                    'name' => substr($item->getName(), 0, 250),
                    'url_key' => $item->getUrlKey(),
                    'image' => $imageArr['image_url'],
                    'thumbnail' => $imageArr['thumbnail_url'],
                    'description' => substr(strip_tags($item->getData('description')), 0, 250),
                    'url_path' => $this->getUrlPath($item->getUrl())
                ];
                if ($refApi == 'catalog.details') {
                        $subCategoriesObj = $item->getChildrenCategories();
                        $subCategories = '';
                    if (!is_array($subCategoriesObj)) {
                        if ($subCategoriesObj->count() > 0) {
                            $subCategoriesObj = $subCategoriesObj->toArray();
                        }
                    }

                    if (count($subCategoriesObj) > 0) {
                        foreach ($subCategoriesObj as $subArr) {
                            $subCategories .= $subArr['entity_id'] . ',';
                        }
                        if ($subCategories != '') {
                            $subCategories = trim($subCategories, ',');
                        }
                    }
                        
                    $categoryCount = 0;
                    if (isset($subCategories) && $subCategories != '') {
                        $idAr = explode(',', $subCategories);
                        $subCategoryCol = $this->categoryCollection->create()
                            ->addIdFilter($idAr)
                            ->addAttributeToFilter('is_active', 1)
                            ->addAttributeToFilter('shop_now', 1);
                        $categoryCount = $subCategoryCol->getSize();
                    }
                    $itemArray['sub_category_count'] = $categoryCount;
                    $itemArray['product_count'] = $item->getProductCollection()->getSize();
                }
                $list[] = $itemArray;
            }
            if ($refApi == 'catalog.details') {
                $data['products_in_current_category'] = $item->getParentCategory()
                    ->getProductCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('visibility', ['gt' => 1])
                    ->setStore($this->getStoreId())
                    ->getSize();
            }
            $total = count($collection);
            if ($collection->getLastPageNumber() > 1) {
                $total = $collection->getSize();
            }
            $data['categories']['list'] = $list;
            $data['categories']['item_count'] = count($collection);
            $data['categories']['total'] = $total;
            $data['categories']['page'] = $page;
            if ($refApi == 'catalog.details') {
                $data['products'] = [];
            }
            $data['status'] = 'success';
        }
        return $data;
    }
    /**
     * Gets Category Details
     *
     * @return array
     */
    public function getCategoryDetail($offset, $limit, $categoryIds)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        return $this->getCategoryList($offset, $limit, $categoryIds, 'category.detail');
    }
    /**
     * Gets Product Details
     *
     * @return array
     */
    public function getProductList($offset, $limit, $categoryId = null, $productSku = null, $refApi = null)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        $page = ($offset / $limit) + 1;
        // Default values
        $rowCnt = 0;
        $valid = false;
        $data['products']['list'] = [];
        $data['products']['item_count'] = 0;
        $data['products']['total'] = 0;
        $data['products']['page'] = 0;
        $data['status'] = 'success';
        $products = null;
        if ($refApi == 'product.detail') { // For product details api
            //add our filter(s) to a group
            /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */
            $valid = false;
            $filter_groups = [];
            $filter_group3 = clone $this->filterGroup;
            $filter3 = clone $this->filter;
            $filter3->setData('field', 'sku');
            $filter3->setData('value', $productSku);
            $filter3->setData('condition_type', 'in');

            $filter_groups[] = $filter_group3->setData('filters', [$filter3]);

            $filter_group4 = clone $this->filterGroup;
            $filter4 = clone $this->filter;
            $filter4->setData('field', 'visibility');
            $filter4->setData('value', "1");
            $filter4->setData('condition_type', 'neq');
            $filter_groups[] = $filter_group4->setData('filters', [$filter4]);
            // Website filter
            $filter_group5 = clone $this->filterGroup;
            $filter5 = clone $this->filter;
            $filter5->setData('field', 'status');
            $filter5->setData('value', 1);
            $filter5->setData('condition_type', 'eq');
            $filter_groups[] = $filter_group5->setData('filters', [$filter5]);
            //add the group(s) to the search criteria object
            /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
            $search_criteria = $this->criteria;
            $search_criteria->setFilterGroups($filter_groups);
            $collection = $this->productRepository->getList($search_criteria);
            $totalCnt = $collection->getTotalCount();
            $rowCnt = 0;
            if ($collection->getTotalCount() > 0) {
                $products = $collection->getItems();
                $valid = true;
            }
        } elseif ($refApi == '' || $refApi == 'catalog.details') { // For product/catalog list api
            if (count($categoryId) < 0 || !is_array($categoryId)) {
                return $data;
            }
            $isRoot = false;
            if ($categoryId[0] == 0) {
                $rootCol = $this->categoryCollection->create()
                    ->addAttributeToSelect('*')
                    ->addIsActiveFilter()
                    ->addAttributeToSelect('store_id')
                    ->addRootLevelFilter();
                $categoryId = [$rootCol->getFirstItem()->getId()];
                $isRoot = true;
            }
            $categoryCol = $this->categoryCollection->create()
                ->addAttributeToSelect('is_anchor')
                ->addIdFilter($categoryId);
            if ($isRoot !== true) {
                $categoryCol->addAttributeToFilter('shop_now', 1);
            }
            if ($categoryCol->getSize() > 0) { // Check category
                if ($categoryCol->getFirstItem()->getProductCollection()->getSize() > 0) {
                    $productIdArray = [];
                    /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $item */
                    foreach ($categoryCol as $item) {
                        $productIdArray = array_merge($productIdArray, $item->getProductCollection()->getAllIds());
                    }
                    $productCount = count($productIdArray);
                    if ($offset <= $productCount) {
                        $page = ($offset / $limit) + 1;
                        //add our filter(s) to a group
                        /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */
                        $filter_groups = [];
                        $filter_group3 = clone $this->filterGroup;
                        $filter3 = clone $this->filter;
                        $filter3->setData('field', 'entity_id');
                        $filter3->setData('value', $productIdArray);
                        $filter3->setData('condition_type', 'in');

                        $filter_groups[] = $filter_group3->setData('filters', [$filter3]);
                        //add the group(s) to the search criteria object
                        /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
                        $search_criteria = $this->criteria;
                        $search_criteria->setFilterGroups($filter_groups);
                        $search_criteria->setCurrentPage($page)->setPageSize($limit);
                        $collection = $this->productRepository->getList($search_criteria);
                        $totalCnt = count($productIdArray);
                        $products = $collection->getItems();
                        $valid = true;
                    }
                }
            }
        }
        if ($valid) {
            /** @var \Magento\Directory\Model\Currency $currency */
            $currency = $this->storeManager->getStore()->getCurrentCurrency();
            $displayName = $this->symbolSystemFactory->create()
                ->getCurrencySymbolsData()[$currency->getCode()]['displayName'];
            $list = [];
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $special_from = null;
                $special_to = null;
                if (isset($product->getData()['special_from_date'])) {
                    $special_from = date('Y-m-d', strtotime($product->getData()['special_from_date']));
                }
                if (isset($product->getData()['special_to_date'])) {
                    $special_to = date('Y-m-d', strtotime($product->getData()['special_to_date']));
                }

                $imageArr = $this->getImageFromData($product, 'product');
                $list[] = [
                    'product_id' => $product->getId(),
                    'sku'=> $product->getSku(),
                    'product_type' => $product->getTypeId(),
                    'catId' => $product->getCategoryIds(),
                    'name' => substr($product->getName(), 0, 250),
                    'description' => substr(strip_tags($product->getData('description')), 0, 250),
                    'short_description' => substr(strip_tags($product->getData('short_description')), 0, 250),
                    'status' => $product->getStatus(),
                    'url_key'=> $product->getUrlKey(),
                    'thumbnail' => $imageArr['thumbnail_url'],
                    'image' => $imageArr['image_url'],
                    'url_path' => $this->getUrlPath($product->getProductUrl()),
                    'currency_code' => $currency->getCode(),
                    'currency_symbol'=> $currency->getCurrencySymbol(),
                    'currency_name' => $displayName,
                    'final_price' => $product->getFinalPrice(),
                    'price' => (float) $product->getData('price'),
                    'special_price' => $product->getSpecialPrice(),
                    'special_from_date' => $special_from,
                    'special_to_date' => $special_to,
                    'cart_url' => 'botgento/cart/add/id/' . $product->getId(),
                ];
                ++ $rowCnt;
            }
            $data['products']['list'] = $list;
            $data['products']['item_count'] = $rowCnt;
            $data['products']['total'] = $totalCnt;
            $data['products']['page'] = $page;
            if ($refApi == 'catalog.details') {
                $data['categories'] = [];
            }
            $data['status'] = 'success';
        }

        return $data;
    }

    /**
     * Gets Product Details
     *
     * @param $offset
     * @param $limit
     * @param $productSku
     * @return array
     */
    public function getProductDetail($offset, $limit, $productSku)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        return $this->getProductList($offset, $limit, null, $productSku, 'product.detail');
    }

    /**
     * Gets Catalog Details
     *
     * @param $offset
     * @param $limit
     * @param $parentCatId
     * @return array
     */
    public function getCatalogDetails($offset, $limit, $parentCatId)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }

        $data = $this->getCategoryList($offset, $limit, $parentCatId, 'catalog.details');
        if (empty($data['categories']['item_count'])) {
            $product = $this->getProductList($offset, $limit, [$parentCatId], null, 'catalog.details');
            if ($product['status'] === 'success') {
                return $product;
            }
        }
        return $data;
    }

    /**
     * Get recent 5 order lists by email
     *
     * @param $email
     * @return array
     */
    public function getOrderLists($email)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        //create our filter
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter1 = clone $this->filter;
        $filter_groups = [];
        //add our filter(s) to a group
        /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */

        $filter_group1 = clone $this->filterGroup;

        $filter1->setData('field', 'customer_email');
        $filter1->setData('value', $email);
        $filter1->setData('condition_type', 'eq');

        $filter_groups[] = $filter_group1->setData('filters', [$filter1]);

        $filter2 = clone $this->filter;
        $filter_group2 = clone $this->filterGroup;

        $filter2->setData('field', 'store_id');
        $filter2->setData('value', $this->getStoreId());
        $filter2->setData('condition_type', 'eq');

        $filter_groups[] = $filter_group2->setData('filters', [$filter2]);

        //add the group(s) to the search criteria object
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
        $search_criteria = $this->criteria;
        $search_criteria->setFilterGroups($filter_groups);
        $search_criteria->setCurrentPage(1)->setPageSize(5);

        $sort = $this->sortOrder;
        $sort->setField('entity_id');
        $sort->setDirection('desc');

        $search_criteria->setSortOrders([$sort]);

        $collection = $this->orderRepo->getList($search_criteria);

        // Default values
        $data = ['orders' => [], 'status' => 'success'];

        if ($collection->getTotalCount() > 0) {
            $orders = $collection->getItems();
            $list = [];
            foreach ($orders as $order) {
                $billing = $order->getBillingAddress();
                $items = $order->getAllVisibleItems();
                $item = end($items);
                $product = $this->productRepository->getById($item->getProductId());
                $imageArr = $this->getImageFromData($product, 'product');
                $list[] = [
                    'order_number' => $order->getIncrementId(),
                    'order_date' => $order->getCreatedAt(),
                    'billing_name' => $billing->getPrefix() . $billing->getFirstname() . ' ' . $billing->getLastname(),
                    'order_amount' => strip_tags($order->formatBasePrice($order->getGrandTotal())),
                    'status' => $order->getStatus(),
                    'order_image' => $imageArr['image_url']
                ];
            }
            $data['orders'] = $list;
            $data['status'] = 'success';
        }

        return $data;
    }

    /**
     * Gets order detail by order increment id
     *
     * @param $orderNo
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderDetail($orderNo)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        //create our filter
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = clone $this->filter;

        $filter_groups = [];
        //add our filter(s) to a group
        /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */
        $filter_group = clone $this->filterGroup;

        $filter->setData('field', 'increment_id');
        $filter->setData('value', $orderNo);
        $filter->setData('condition_type', 'eq');

        $filter_groups[] = $filter_group->setData('filters', [$filter]);

        $filter2 = clone $this->filter;
        $filter_group2 = clone $this->filterGroup;

        $filter2->setData('field', 'store_id');
        $filter2->setData('value', $this->getStoreId());
        $filter2->setData('condition_type', 'eq');

        $filter_groups[] = $filter_group2->setData('filters', [$filter2]);

        //add the group(s) to the search criteria object
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
        $search_criteria = $this->criteria;
        $search_criteria->setFilterGroups($filter_groups);

        $data = ['payload' => [], 'status' => 'success'];

        $collection = $this->orderRepo->getList($search_criteria);
        if ($collection->getTotalCount()) {
            $items = $collection->getItems();
            $itemArray= [];
            $payLoad = [];
            foreach ($items as $item) {
                foreach ($item->getAllVisibleItems() as $product) {
                    $price = $product->getPrice();
                    /** @var \Magento\Catalog\Model\Product $prd */
                    $prd = $this->productRepository->getById($product->getProductId());

                    $imageUrl = $this->getImageFromData($prd, 'product', 'image_url', 'order_image');
                    $itemArray[] = [
                        'title' => substr($prd->getName(), 0, 250),
                        'subtitle' => !empty($prd->getDescription())
                            ? substr(explode("\n", strip_tags($prd->getDescription()))[0], 0, 255) : $prd->getName(),
                        'quantity' => abs($item->getQtyOrdered()),
                        'price' => $price,
                        'currency' => 'USD',
                        'image_url' => $imageUrl
                    ];
                }
                /** Adjustments data array */
                $adjustments = [];
                $storeId = $item->getStoreId();
                $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                $customerGroupId = $item->getCustomerGroupId();
                $orderDate = $this->localeDate->date($item->getCreatedAt())->format('m/d/y H:i:s');
                $ordTimeStamp = strtotime($orderDate);

                $couponCode = $item->getCouponCode();
                $ruleIndex = 0;
                if ($couponCode) {
                    $salesRuleId = $this->coupon->loadByCode($couponCode)->getRuleId();
                    $salesRule = $this->salesRule->load($salesRuleId);
                    $adjustments[] = [
                        'name' => $salesRule->getName(),
                        'code' => $couponCode,
                        'amount' => $salesRule->getDiscountAmount()
                    ];
                    ++$ruleIndex;
                }

                $oldRuleName = '';
                foreach ($item->getAllItems() as $rule) {
                    $productId = $rule->getProductId();
                    $isValid = $this->catRulesRes
                        ->getRulesFromProduct($ordTimeStamp, $websiteId, $customerGroupId, $productId);
                    if (!empty($isValid)) {
                        $ruleItem = $this->catRule->load($isValid[0]['rule_id']);
                        if ($oldRuleName != $ruleItem->getName()) {
                            $oldRuleName = $ruleItem->getName();
                        } elseif ($oldRuleName == $ruleItem->getName()) {
                            continue;
                        }

                        $adjustments[] = ['name' => $ruleItem->getName(), 'amount' => $ruleItem->getDiscountAmount()];
                        ++$ruleIndex;
                    }
                }
                $instance = $item->getPayment()->getMethodInstance();
                if ($item->getPayment()->getCcLast4()) {
                    $method = $instance->getTitle() . ' ';
                    $method .= $item->getPayment()->getCcLast4();
                } else {
                    $method = $instance->getTitle();
                }
                $billing = $item->getBillingAddress();
                $region = $billing->getRegion() ? $billing->getRegion() : $billing->getRegionCode();
                $region = !empty($region) ? $region : '';

                $payLoad = [
                    'template_type' => 'receipt',
                    'recipient_name' => $billing->getPrefix() .
                        $billing->getFirstname() . ' ' .
                        $billing->getLastname(),
                    'order_number' => $item->getIncrementId(),
                    'currency' => $item->getOrderCurrencyCode(),
                    'payment_method' => $method,
                    'order_url' => $this->_urlBuilder->getUrl('sales/order') . 'view/order_id/' . $item->getId(),
                    'timestamp' => strtotime($item->getCreatedAt()),
                    'address' => [
                        'street_1' => $item->getBillingAddress()->getStreetLine(1),
                        'street_2' => $item->getBillingAddress()->getStreetLine(2),
                        'city' => $item->getBillingAddress()->getCity(),
                        'postal_code' => $item->getBillingAddress()->getPostcode(),
                        'state' => $region,
                        'country' => $item->getBillingAddress()->getCountryId()
                    ],
                    'summary' => [
                        'subtotal' => $item->getSubtotal(),
                        'shipping_cost' => $item->getShippingAmount(),
                        'total_tax' => $item->getTaxAmount(),
                        'total_cost' => $item->getGrandTotal()
                    ],
                    'adjustments' => $adjustments,
                    'elements' => $itemArray
                ];
            }
            $data['payload'] = json_encode($payLoad);
            $data['status'] = 'success';
        }
        return $data;
    }

    public function getProductImageUrl(
        $product
    ) {
        $imageUrl = null;
        $image = $product->getData('image');
        /** @var \Magento\Framework\Image\Adapter\Gd2 $imageResize */
        if ($image && $image != 'no_selection') {
            $absolutePath = $this->fileSystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('catalog/product') . $image;
            $writeImage = $this->fileSystem->getDirectoryWrite(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('catalog/product/resize/') . 265 . $image;
            //resize image and store it to location
            $this->resizeAndSaveImage($absolutePath, $writeImage, 265, 265);
            $imageUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/product/resize/' . 265 . $image;
        } else {
            $imageUrl = $this->getPlaceHolderImage('product', 265, 265);
        }
        return $imageUrl;
    }
    /**
     * Gets customer wishlists by email
     *
     * @param $offset
     * @param $limit
     * @param $email
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getWishlistItems($offset, $limit, $email)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }
        $customer = $this->customerItem->getCollection()
            ->addFieldToFilter('store_id', $this->getStoreId())
            ->addFieldToFilter('email', $email)->setPageSize(1)->getFirstItem();

        $wishlistCol = $this->wishlist->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
            ->setPageSize(1)->getFirstItem();
        $ids = $wishlistCol->getItemCollection()->getColumnValues('product_id');

        $list = [];
        // Default values
        $data = ['wishlist' => ['items' => $list]];
        $data['wishlist']['item_count'] = 0;
        $data['wishlist']['total'] = 0;
        $data['wishlist']['page'] = 0;
        $data['status'] = 'success';
        if (empty($ids)) {
            return $data;
        }
        $filter_groups = [];
        //create our filter
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = clone $this->filter;

        //add our filter(s) to a group
        /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */
        $filter_group = clone $this->filterGroup;
        $valid = true;

        $filter->setData('field', 'entity_id');
        $filter->setData('value', $ids);
        $filter->setData('condition_type', 'in');

        $filter_groups[] = $filter_group->setData('filters', [$filter]);

        $filter2 = clone $this->filter;
        $filter_group2 = clone $this->filterGroup;

        $filter2->setData('field', 'website_id');
        $filter2->setData('value', $this->getWebsiteId());
        $filter2->setData('condition_type', 'eq');

        $filter_groups[] = $filter_group2->setData('filters', [$filter2]);
        //add the group(s) to the search criteria object
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
        $page = ($offset / $limit) + 1;
        $search_criteria = $this->criteria;
        $search_criteria->setFilterGroups($filter_groups);
        $search_criteria->setCurrentPage($page)->setPageSize($limit);

        $collection = $this->productRepository->getList($search_criteria);

        $rowCnt = 0;
        if ($collection->getTotalCount() > 0 && $valid && $offset <= $collection->getTotalCount()) {
            $products = $collection->getItems();
            /** @var \Magento\Directory\Model\Currency $currency */
            $currency = $this->storeManager->getStore()->getCurrentCurrency();
            $displayName = $this->symbolSystemFactory->create()
                ->getCurrencySymbolsData()[$currency->getCode()]['displayName'];
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                $special_from = null;
                $special_to = null;
                if (isset($product->getData()['special_from_date'])) {
                    $special_from = date('Y-m-d', strtotime($product->getData()['special_from_date']));
                }
                if (isset($product->getData()['special_to_date'])) {
                    $special_to = date('Y-m-d', strtotime($product->getData()['special_to_date']));
                }
                $imageArr = $this->getImageFromData($product, 'product');
                $list[] = [
                    'product_id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'product_type' => $product->getTypeId(),
                    'catId' => $product->getCategoryIds(),
                    'name' => substr($product->getName(), 0, 250),
                    'description' => substr(strip_tags($product->getData('description')), 0, 250),
                    'short_description' => substr(strip_tags($product->getData('short_description')), 0, 250),
                    'status' => $product->getStatus(),
                    'url_key' => $product->getUrlKey(),
                    'thumbnail' => $imageArr['thumbnail_url'],
                    'image' => $imageArr['image_url'],
                    'url_path' => $this->getUrlPath($product->getProductUrl()),
                    'currency_code' => $currency->getCode(),
                    'currency_symbol' => $currency->getCurrencySymbol(),
                    'currency_name' => $displayName,
                    'final_price' => $product->getFinalPrice(),
                    'price' => (float) $product->getData('price'),
                    'special_price' => $product->getSpecialPrice(),
                    'special_from_date' => $special_from,
                    'special_to_date' => $special_to,
                    'cart_url' => 'botgento/cart/add/id/' . $product->getId(),
                ];
                ++$rowCnt;
            }
            $data['wishlist']['items'] = $list;
            $data['wishlist']['item_count'] = $rowCnt;
            $data['wishlist']['total'] = $collection->getTotalCount();
            $data['wishlist']['page'] = $page;
            $data['status'] = 'success';
        }

        return $data;
    }

    /**
     * Delete all data
     * @return array
     */
    public function purgeBotData()
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }

        $filter_group = $this->filterGroup;
        $search_criteria = $this->criteria;
        $search_criteria->setFilterGroups([$filter_group]);
        $cronData = $this->cronData->getList($search_criteria, $this->getStoreId())->getItems();
        $customerEmails = [];
        foreach ($cronData as $cronDatum) {
            $this->cronData->deleteById($cronDatum->getId());
            $customerEmails[] = $cronDatum->getCustomerEmail();
        }
        $fbData = $this->fbRecipent->getList()
            ->addFieldToFilter('customer_email', ['in' => $customerEmails]);

        foreach ($fbData as $fbDatum) {
            $this->fbRecipent->deleteById($fbDatum->getId());
        }

        $websiteId = $this->getWebsiteId();
        $configWriter = $this->configWriter;
        $configWriter->delete(self::STATUS, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::VALID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::API_TOKEN, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::APP_ID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::PAGE_ID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::HEX_CODE, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::FB_CHECKBOX, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::FB_BUTTON, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::SND_ORDER_CNF, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::SND_ORDER_CNF_AFTER, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $configWriter->delete(self::SND_SHIP_CNF, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        return ['status' => 'success'];
    }

    /**
     * Gets Product/Category place holder image
     *
     * @param $type
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getPlaceHolderImage($type, $width = self::IMG_WIDTH, $height = self::IMG_HEIGHT)
    {
        $configPath = 'catalog/placeholder/image_placeholder';
        $dbPlaceHolder = $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId()
        );
        $absolutePath = null;
        if ($dbPlaceHolder) {
            $defaultUrl = 'catalog/product/placeholder/' . $dbPlaceHolder;
            $filesystem = $this->fileSystem->getDirectoryRead('media', 'file');
            $absolutePath = $filesystem->getAbsolutePath($defaultUrl);
        } else {
            $defaultUrl = 'Magento_Catalog::images/product/placeholder/image.jpg';
            $_assetSource = $this->assetSource;
            $params = ['module' => 'Magento_Catalog'];
            $img = $_assetSource->createAsset($defaultUrl, $params);
            $absolutePath = $img->getSourceFile();
        }
        $writeImage = $this->fileSystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath('catalog/' . $type . '/resize/') . $width . 'x' . $height . '/image.jpg';

        //resize image and store it to location
        $this->resizeAndSaveImage($absolutePath, $writeImage, $width, $height);

        $thumbnail = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'catalog/' . $type . '/resize/' . $width . 'x' . $height . '/image.jpg';
        return $thumbnail;
    }

    /**
     * Gets Facebook Message button
     *
     * @param $page
     * @return array|mixed
     */
    public function getFbMessageButton($page)
    {
        $customerEmail = $this->customer->getCustomer()->getEmail()!=null?$this->customer->getCustomer()->getEmail():'';
        $websiteId = $this->getWebsiteId();
        $status = $this->scopeConfig->getValue(
            self::STATUS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $fbBtnStatus = $this->scopeConfig->getValue(
            self::FB_BUTTON,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if (!$status || !$fbBtnStatus) {
            return ['code' => 423, 'status' => 'success'];
        }
        $api_token = $this->scopeConfig->getValue(
            self::API_TOKEN,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $hexCode = $this->scopeConfig->getValue(
            self::HEX_CODE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if (empty($hexCode)) {
            return ['status' => 'success'];
        }
        $curl = $this->curl;
        $curl->addHeader('Authorization', $api_token);
        $params = ['page' => $page];
        if (!empty($customerEmail)) {
            $params['email'] = $customerEmail;
        }

        $apiurl = $this->getApiUrl($hexCode, 'get-messageus-btn');
        $curl->post($apiurl, $params);

        return json_decode($curl->getBody(), true);
    }

    /**
     * Communicates with Botgento server
     *
     * @return array
     */
    public function getPing()
    {
        return [
            'status'    =>  'success',
            'code'      =>  200
        ];
    }

    /**
     * Saves Configurations from botgento
     *
     * @param $options
     * @return array
     */
    public function getConfigDetailsSave($options, $header)
    {
        if (is_array($options)) {
            $writer = $this->configWriter;
            $websiteId = $this->getWebsiteId();
            $apiToken = $this->scopeConfig->getValue(
                self::API_TOKEN,
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
            if ($apiToken != $header && !empty($apiToken)) {
                return ['status' => 'fail', 'message' => 'Invalid api token'];
            }
            if ($apiToken != $options['website_api_token'] && !empty($apiToken)) {
                return ['status' => 'fail', 'message' => 'Invalid api token'];
            }
            if ($apiToken !== $options['website_api_token'] && empty($apiToken)) {
                $writer->save(
                    self::STATUS,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::VALID,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::API_TOKEN,
                    $options['website_api_token'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::HEX_CODE,
                    $options['website_hash'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::PAGE_ID,
                    $options['fb_page_id'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::APP_ID,
                    $options['fb_app_id'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::SND_SHIP_CNF,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::SND_ORDER_CNF,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::FB_BUTTON,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::FB_CHECKBOX,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
            } else {
                $writer->save(
                    self::VALID,
                    1,
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::HEX_CODE,
                    $options['website_hash'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::APP_ID,
                    $options['fb_app_id'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
                $writer->save(
                    self::PAGE_ID,
                    $options['fb_page_id'],
                    ScopeInterface::SCOPE_WEBSITES,
                    $websiteId
                );
            }
            return ['status' => 'success', 'code' => 200, 'message' => 'Updated values on server!'];
        }
        return ['status' => 'fail', 'code' => 400, 'message' => 'Invalid variable type'];
    }

    public function sendStatus($websiteId)
    {
        $curl = $this->curl;
        $auth_ID = $auth_ID = $this->scopeConfig->getValue(
            self::API_TOKEN,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $hexCode = $this->scopeConfig->getValue(
            self::HEX_CODE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if (empty($hexCode)) {
            return;
        }
        $status = false;
        if ($this->isModuleOutputEnabled('Botgento_Base')) {
            $data = $this->_getRequest()->getParams();
            if (isset($data['groups']['base']['fields']['status']['value'])) {
                $status = $data['groups']['base']['fields']['status']['value'];
            } else {
                $status = $data['groups']['base']['fields']['status']['inherit'];
            }
        }

        $curl->addHeader('Authorization', $auth_ID);
        $payload = ['is_extension_enable' => (boolean) $status];

        $apiurl = $this->getApiUrl($hexCode, 'set-extension-status');
        $curl->post($apiurl, $payload);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function getUrlPath($url)
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl();
        return str_replace($baseUrl, '', $url);
    }

    /**
     * @return array
     */
    public function authCheck()
    {
        $auth = $this->_getRequest()->getHeader('Authorization');
        $auth_ID = $this->scopeConfig->getValue(
            self::API_TOKEN,
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId()
        );

        if (!empty($auth) && $auth == $auth_ID) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'fail', 'message' => __('Authorization Failed') ];
        }
    }

    /**
     * @param $ordIds
     * @return array
     */
    public function getWebsiteIds($ordIds)
    {
        //create our filter
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter1 = clone $this->filter;
        $filter_groups = [];
        //add our filter(s) to a group
        /** @var \Magento\Framework\Api\Search\FilterGroup $filter_group */

        $filter_group1 = clone $this->filterGroup;

        $filter1->setData('field', 'increment_id');
        $filter1->setData('value', $ordIds);
        $filter1->setData('condition_type', 'in');

        $filter_groups[] = $filter_group1->setData('filters', [$filter1]);

        //add the group(s) to the search criteria object
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $search_criteria */
        $search_criteria = $this->criteria;
        $search_criteria->setFilterGroups($filter_groups);
        $search_criteria->setCurrentPage(1)->setPageSize(5);

        $sort = $this->sortOrder;
        $sort->setField('entity_id');
        $sort->setDirection('asc');

        $search_criteria->setSortOrders([$sort]);

        $collection = $this->orderRepo->getList($search_criteria);

        // Default values
        $data = [];
        if ($collection->getTotalCount()) {
            $items = $collection->getItems();
            $websiteIds = [];
            foreach ($items as $item) {
                $websiteId = $this->storeManager->getStore($item->getStoreId())->getWebsiteId();
                $websiteIds['website_id'][] = $websiteId;
                $websiteIds['order_ids_' . $websiteId][] = $item->getIncrementId();
            }
            return $websiteIds;
        }
        return $data;
    }

    public function getApiTokenPath()
    {
        return self::API_TOKEN;
    }
    public function getVerifyUrl()
    {
        return self::VERIFY_URL;
    }
    public function getValidPath()
    {
        return self::VALID;
    }
    public function getStatusPath()
    {
        return self::STATUS;
    }
    public function getHexCodePath()
    {
        return self::HEX_CODE;
    }
    public function getPageIdPath()
    {
        return self::PAGE_ID;
    }
    public function getAppIdPath()
    {
        return self::APP_ID;
    }
    public function getSendOrderCnfPath()
    {
        return self::SND_ORDER_CNF;
    }
    public function getSendOrderCnfAfterPath()
    {
        return self::SND_ORDER_CNF_AFTER;
    }
    public function getSendOrderShipPath()
    {
        return self::SND_SHIP_CNF;
    }
    public function getFbCheckboxStatusPath()
    {
        return self::FB_CHECKBOX;
    }

    /**
     * Check module is enable and is valid or not
     *
     * @return array
     */
    public function getModuleIsEnableAndValid()
    {
        if ($this->getConfig(Self::STATUS) && ($this->getConfig(Self::VALID) > 0)) {
            return true;
        }
        return false;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getWebsiteId($storeId = null)
    {
        $websiteId = $this->websiteId;
        if (!$websiteId) {
            if (!$storeId) {
                $storeId = $this->getStoreId();
            }
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $this->setWebsiteId($websiteId);
        }
        return $websiteId;
    }

    /**
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId = 0)
    {
        if ($websiteId) {
            $this->websiteId = $websiteId;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->storeId;
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->setStoreId($storeId);
        }
        return $storeId;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId = 0)
    {
        if ($storeId) {
            $this->storeId = $storeId;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHexCode()
    {
        return $this->scopeConfig->getValue(
            $this->getHexCodePath(),
            ScopeInterface::SCOPE_WEBSITE,
            $this->getWebsiteId($this->getStoreId())
        );
    }

    /**
     * @param $path
     * @return mixed|null
     */
    public function getConfigValue($path)
    {
        if ($path) {
            return $this->scopeConfig->getValue(
                $path,
                ScopeInterface::SCOPE_WEBSITE,
                $this->getWebsiteId($this->getStoreId())
            );
        }
        return null;
    }

    /**
     * @param $hexcode
     * @param string $action
     * @return string
     */
    public function getApiUrl($hexcode, $action = '')
    {
        $url = self::API_HTTP . '://'. self::API_HOST . self::API_PATH;
        $url .= !empty($hexcode)?$hexcode:$this->getHexCode();
        $url .= !empty($action)?"/$action":'';

        return $url;
    }

    /**
     * @return string
     */
    public function getBotgentoUrl()
    {
        $url = self::API_HTTP . '://'. self::API_HOST.'/';
        return $url;
    }

    /**
     * Save attribute data to table
     *
     * @param array $options
     * @return array
     */
    public function saveAttributeDataToTable($options)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }

        $allowedType = ['abandoned_cart','product_in_stock','orders'];

        if (in_array($options['type'], $allowedType)) {
            $atributesJsonData = $this->jsonHelper->jsonEncode($options['attributes_json']);

            $collection = $this->syncAttributesFactory->create()
                ->getCollection()
                ->addFieldToFilter("type", $options['type']);

            if (count($collection) == 0) {
                $syncAttributesModel = $this->syncAttributesFactory->create();
                $syncAttributesModel->setType($options['type']);
                $syncAttributesModel->setAttributesJson($atributesJsonData);
                $syncAttributesModel->setCreatedAt($this->dateTime->gmtTimestamp());
                $syncAttributesModel->setUpdatedAt($this->dateTime->gmtTimestamp());
                $syncAttributesModel->save();
            } else {
                $collection = $collection->getFirstItem();
                $syncAttributesModel = $this->syncAttributesFactory->create()->load($collection->getId());
                $syncAttributesModel->setAttributesJson($atributesJsonData);
                $syncAttributesModel->setUpdatedAt($this->dateTime->gmtTimestamp());
                $syncAttributesModel->save();
            }
            $response = [
                'status'=>'Success'
            ];
        } else {
            $response = [
                'status'=>'Success',
                'message'=>'There is no matching Type found for sync attributes.'
            ];
        }
        return $response;
    }

    /**
     * Save attribute data to table
     *
     * @param array $options
     * @return array
     */
    public function getOrderStatusFromQuote($options)
    {
        $authResult = $this->authCheck();
        if ($authResult['status'] == 'fail') {
            return $authResult;
        }

        $quoteId = $options['quote_id'];
        $orderCollection = $this->orderCollectionFactory->create()->addFieldToFilter('quote_id', $quoteId);

        if (!empty($orderCollection->getSize())) {
            $response = [
                'total_orders'=>'1',
                'status'=>'success'
            ];
        } else {
            $response = [
                'total_orders'=>'0',
                'status'=>'success'
            ];
        }

        return $response;
    }

    /**
     * Get customer email from session
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        $customerEmail = '';
        if ($this->customerSession->isLoggedIn()) {
            $customerEmail = $this->customerSession->getCustomer()->getEmail();
        }
        return $customerEmail;
    }

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionManager->getSessionId();
    }

    /**
     * Get current uri
     *
     * @return string
     */
    public function getCurrentUri()
    {
        $currentWebsiteUrl = $this->_urlBuilder->getCurrentUrl();
        return $this->getUrlPath($currentWebsiteUrl);
    }

    /**
     * Get full action name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->_getRequest()->getFullActionName();
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        $cookie = $this->cookieManager->getCookie(self::BGC_UUID_COOKIE_NAME);
        if (!empty($cookie)) {
            return $cookie;
        } else {
            $uuid = md5($this->getSessionId());

            $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setPath($this->_getRequest()->getBasePath())
                ->setDuration(86400*24);
            $this->cookieManager->setPublicCookie('bgc_uuid', $uuid, $metadata);
        }
        return $uuid;
    }

    /**
     * Get api token
     *
     * @return string
     */
    public function getApiToken()
    {

        $websiteId = $this->getWebsiteId();
        return $this->scopeConfig->getValue(
            self::API_TOKEN,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get website hash
     *
     * @return string
     */
    public function getWebsiteHash()
    {
        $websiteId = $this->getWebsiteId();
        return $this->scopeConfig->getValue(
            self::HEX_CODE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get api token by website id
     *
     * @param $websiteId
     * @return string
     */
    public function getApiTokenByWebsiteId($websiteId)
    {
        return $this->scopeConfig->getValue(
            self::API_TOKEN,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get website hash by website id
     *
     * @param $websiteId
     * @return string
     */
    public function getWebsiteHashByWebsiteId($websiteId)
    {
        return $this->scopeConfig->getValue(
            self::HEX_CODE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Convert price from base currency to quote currency
     *
     * @param $price
     * @param $baseCurrencyCode
     * @param $quoteCurrencyCode
     * @return string
     */
    public function convertPriceFromCurrencyCode($price, $baseCurrencyCode, $quoteCurrencyCode)
    {
        if ($baseCurrencyCode == $quoteCurrencyCode) {
            return $price;
        }
        return $this->directoryHelper->currencyConvert($price, $baseCurrencyCode, $quoteCurrencyCode);
    }

    /**
     * Get Abandon Cart Data for syc with botgento
     *
     * @param $storesArray
     * @return array
     */
    public function getAbandonedCartData($storesArray)
    {
        $syncLogModel = $this->syncLogFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'abandoned_cart')
            ->addFieldToFilter('status', 'success');

        if ($syncLogModel->getSize()) {
            $syncLogModel = $syncLogModel->getLastItem();
            $lastSnycDateTime = date('Y-m-d H:i:s', strtotime($syncLogModel->getCreatedAt()));
        }

        $quoteCollection = $this->quoteFactory->create()
            ->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', ["in" => $storesArray]);

        if (isset($lastSnycDateTime)) {
            $quoteCollection->addFieldToFilter('updated_at', ['from'=>$lastSnycDateTime]);
        }

        $data = [];
        $cartItemsArray = [];
        $billingAddressItemsArray = [];
        $shippingAddressItemsArray = [];

        $attributeJson = '{"0":"entity_id","1":"created_at","2":"updated_at","3":"is_virtual","4":"items_count","5":"items_qty","6":"store_currency_code","7":"quote_currency_code","8":"base_currency_code","9":"base_grand_total","10":"customer_email","11":"coupon_code","12":"base_subtotal","13":"base_subtotal_with_discount","cart_items":["product_id","created_at","updated_at","sku","name","short_description","qty","price","final_price","discount_percent","discount_amount","row_total","product_type","thumbnail","image","currency_code","currency_symbol"],"address":{"billing":["street","city","region","postcode","country_id"],"shipping":["street","city","region","postcode","country_id"]}}';

        $attributeArray = $this->jsonHelper->jsonDecode($attributeJson);

        if (isset($attributeArray['cart_items'])) {
            $cartItemsArray = $attributeArray['cart_items'];
        }
        if (isset($attributeArray['address']['billing'])) {
            $billingAddressItemsArray = $attributeArray['address']['billing'];
        }
        if (isset($attributeArray['address']['shipping'])) {
            $shippingAddressItemsArray = $attributeArray['address']['shipping'];
        }

        unset($attributeArray['cart_items']);
        unset($attributeArray['address']);

        $sncAttributeCollection = $this->syncAttributesFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'abandoned_cart');

        if ($sncAttributeCollection->getSize()) {
            $syncCartItemsArray = [];
            $syncBillingAddressItemsArray = [];
            $syncShippingAddressItemsArray = [];

            $sncAttributeCollection = $sncAttributeCollection->getLastItem();
            $snycAttributeJson = $sncAttributeCollection->getAttributesJson();

            $syncAttributeArray = $this->jsonHelper->jsonDecode($snycAttributeJson);

            if (isset($syncAttributeArray['cart_items'])) {
                $syncCartItemsArray = $syncAttributeArray['cart_items'];
            }

            if (isset($syncAttributeArray['address']['billing'])) {
                $syncBillingAddressItemsArray = $syncAttributeArray['address']['billing'];
            }

            if (isset($syncAttributeArray['address']['shipping'])) {
                $syncShippingAddressItemsArray = $syncAttributeArray['address']['shipping'];
            }

            unset($syncAttributeArray['cart_items']);
            unset($syncAttributeArray['address']);

            if (!empty($syncAttributeArray) && is_array($syncAttributeArray)) {
                $attributeArray = array_unique(array_merge($attributeArray, $syncAttributeArray));
            }

            if (!empty($syncCartItemsArray) && is_array($syncCartItemsArray)) {
                $cartItemsArray = array_unique(array_merge($cartItemsArray, $syncCartItemsArray));
            }

            if (!empty($syncBillingAddressItemsArray) && is_array($syncBillingAddressItemsArray)) {
                $billingAddressItemsArray = array_unique(
                    array_merge($billingAddressItemsArray, $syncBillingAddressItemsArray)
                );
            }

            if (!empty($syncShippingAddressItemsArray) && is_array($syncShippingAddressItemsArray)) {
                $shippingAddressItemsArray = array_unique(
                    array_merge($shippingAddressItemsArray, $syncShippingAddressItemsArray)
                );
            }
        }

        foreach ($quoteCollection as $quote) {
            $baseCurrencyCode = $quote->getBaseCurrencyCode();

            $subscriberMappingCollection = $this->subscriberMappingCollectionFactory->create()
                ->addFieldToFilter("quote_id", $quote->getId())
                ->getLastItem();

            if ($subscriberMappingCollection->hasData() && $subscriberMappingCollection->getIsButtonPress() == 1) {
                foreach ($attributeArray as $value) {
                    $tempData[$value] = $quote->getData($value);
                }

                $tempData['bgc_uuid'] = $subscriberMappingCollection->getUuid();

                $itemCollection = $quote->getItemsCollection();
                $itemCollection->addFieldToFilter('parent_item_id', ['null' => true]);

                foreach ($itemCollection as $item) {
                    $product = $this->productRepository->getById(
                        $item->getProductId(),
                        false,
                        $quote->getStoreId()
                    );

                    $urlPath =  $this->getUrlPath($product->getProductUrl());

                    if (strpos($urlPath, '?') !== false) {
                        $urlPathArray = explode('?', $urlPath);
                        $urlPath = $urlPathArray[0];
                    }

                    $imageArr = $this->getImageFromData($product, 'product');
                    $productData = [
                        "product_id" => $item->getProductId(),
                        "created_at" => $item->getCreatedAt(),
                        "updated_at" => $item->getUpdatedAt(),
                        "sku" => $item->getSku(),
                        "name" => $item->getName(),
                        "short_description" => substr(strip_tags($product->getShortDescription()), 0, 250),
                        "qty" => $item->getQty(),
                        "price" => number_format($item->getBasePrice(), 2),
                        "final_price" => number_format($product->getFinalPrice(), 2),
                        "discount_percent" => number_format($item->getDiscountPercent(), 2),
                        "discount_amount" => number_format($item->getBaseDiscountAmount(), 2),
                        "row_total" => number_format($item->getBaseRowTotal(), 2),
                        "product_type" => $item->getProductType(),
                        "thumbnail" => $imageArr['thumbnail_url'],
                        "image" => $imageArr['image_url'],
                        "url_path" => $urlPath,
                        "currency_code" => $baseCurrencyCode,
                        "currency_symbol" => $this->localeCurrency->getCurrency($baseCurrencyCode)->getSymbol(),
                    ];

                    $extraProductAttributes = array_diff($cartItemsArray, array_keys($productData));

                    foreach ($extraProductAttributes as $extraAttribute) {
                        if ($item->hasData($extraAttribute) && ($item->getData($extraAttribute)) != null) {
                            $productData[$extraAttribute] = $item->getData($extraAttribute);
                        } else {
                            $productData[$extraAttribute] = $product->getData($extraAttribute);
                        }
                    }

                    $tempData['cart_item'][] = $productData;
                    unset($productData);
                }
                foreach ($billingAddressItemsArray as $value) {
                    $tempData['address']['billing'][$value] = $quote->getBillingAddress()->getData($value);
                }

                foreach ($shippingAddressItemsArray as $value) {
                    $tempData['address']['shipping'][$value] = $quote->getShippingAddress()->getData($value);
                }

                $data[] = $tempData;
                unset($tempData);
            }
        }

        return $data;
    }

    /**
     * Get back in stock alert data
     *
     * @param $website
     * @return array
     */
    public function getInStockAlertData($website)
    {
        $websiteId = $website->getId();

        $data = [];

        $inStockAlertCollection = $this->inStockAlertCollectionFactory->create()
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('is_notification_sent', 0)
            ->addFieldToSelect('product_id');

        if ($inStockAlertCollection->getSize()) {
            $inStockAlertCollection->getSelect()
                ->group('product_id');

            $store = $website->getDefaultStore();
            $currencyCode = $store->getCurrentCurrencyCode();
            $currencySym = $this->localeCurrency->getCurrency($currencyCode)->getSymbol();
            $currencyName = $this->localeCurrency->getCurrency($currencyCode)->getName();

            foreach ($inStockAlertCollection as $inStockAlert) {
                $product = $this->productRepository->getById(
                    $inStockAlert->getProductId(),
                    false,
                    $website->getDefaultStore()->getId()
                );

                if ($product->isSalable()) {
                    $tempData = [];

                    $uuidInStockAlertCollection = $this->inStockAlertCollectionFactory->create()
                        ->addFieldToFilter('website_id', $websiteId)
                        ->addFieldToFilter('product_id', $inStockAlert->getProductId())
                        ->addFieldToFilter('is_notification_sent', 0)
                        ->addFieldToSelect('uuid')
                        ->addFieldToSelect('id');

                    $uuidArray = [];
                    $instockALertIds = [];
                    foreach ($uuidInStockAlertCollection as $uuidInStockAlertData) {
                        $uuidArray[] = $uuidInStockAlertData->getUuid();
                        $instockALertIds[] = $uuidInStockAlertData->getId();
                    }

                    $tempData['bgc_uuid'] = $uuidArray;
                    $tempData['instock_ids'] = $instockALertIds;
                    unset($uuidArray);
                    unset($instockALertIds);

                    $urlPath =  $this->getUrlPath($product->getProductUrl());

                    if (strpos($urlPath, '?') !== false) {
                        $urlPathArray = explode('?', $urlPath);
                        $urlPath = $urlPathArray[0];
                    }
                    $imageArr = $this->getImageFromData($product, 'product');
                    $tempData['product'] = [
                        "product_id" => $product->getId(),
                        "sku" => $product->getSku(),
                        "product_type" => $product->getTypeID(),
                        "catId" => $product->getCategoryIds(),
                        "name" => $product->getName(),
                        "description" => substr(strip_tags($product->getDescription()), 0, 250),
                        "short_description" => substr(strip_tags($product->getShortDescription()), 0, 250),
                        "status" => $product->getStatus(),
                        "url_key" => $product->getUrlKey(),
                        "thumbnail" => $imageArr['thumbnail_url'],
                        "image" => $imageArr['image_url'],
                        "url_path" => $urlPath,
                        "currency_code" => $currencyCode,
                        "currency_symbol" => $currencySym,
                        "currency_name" => $currencyName,
                        "price" => number_format($product->getPrice(), 2),
                        "final_price" => number_format($product->getFinalPrice(), 2),
                        "special_price" => number_format($product->getSpecialPrice(), 2),
                        "special_from_date" => $product->getSpecialFromDate(),
                        "special_to_date" => $product->getSpecialToDate(),
                        "cart_url" => "botgento/cart/add/id/".$product->getId()
                    ];

                    $data[] = $tempData;
                    unset($tempData);
                }
            }
        }

        return $data;
    }

    /*
     * Get Page title
     */
    public function getPageTitle()
    {

        return $this->pageTitle->getShort();
    }
    
    /**
     * Generate bgc value
     *
     * @return string
     */
    public function genBGCValue()
    {
        $customerEmail = $this->getCustomerEmail();

        $uuid = $this->getUuid();

        $currentUri = $this->getCurrentUri();

        $curentPageTitle = '';
        $curentPageTitle = $this->getPageTitle();

        if (!empty($curentPageTitle)) {
            $curentPageTitle = str_replace(
                ' ',
                '-',
                str_replace(' - ', ' ', strtolower($curentPageTitle))
            );
        }
        $fullActionName = $this->getFullActionName();
        $data = [
            'email'=> $customerEmail,
            'bgc_uuid' => $uuid,
            'uri'=> $currentUri,
            'page'=> $curentPageTitle,
            'page_type'=> $fullActionName
        ];

        $encryption_key = $this->getApiToken();
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_iv = $this->getWebsiteHash();

        $key = hash('sha256', $encryption_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_encrypt(json_encode($data), $encrypt_method, $key, 0, $iv);
    }

    /**
     * For: Resize the image base on the request and save it to associate location
     * @param $absolutePath
     * @param $writeImage
     * @param string $width
     * @param string $height
     * @return mixed
     */
    public function resizeAndSaveImage($absolutePath, $writeImage, $width = '', $height = '')
    {
        if (empty($width)) {
            $width = self::IMG_WIDTH;
        }
        if (empty($height)) {
            $height = self::IMG_HEIGHT;
        }
        //create image factory...
        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(false);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->resize($width, $height);
        //destination folder
        $destination = $writeImage;
        //save image
        return $imageResize->save($destination);
    }

    /**
     * For: Extract image and resize for the FB formate from catalog, product
     * @param $dataInput
     * @param string $dataType
     * @param string $imageType
     * @return array
     */
    public function getImageFromData($dataInput, $dataType = 'product', $returnType = '', $imageType = '')
    {
        $thumbnail = null;
        $imageUrl = null;
        $image = $dataInput->getImage();
        $imageWidth = self::IMG_WIDTH;
        $imageHeight = self::IMG_HEIGHT;
        if (!empty($imageType) && $imageType == 'order_image') {
            $imageWidth = self::ORDER_IMG_WIDTH;
            $imageHeight = self::ORDER_IMG_HEIGHT;
        }

        /** @var \Magento\Framework\Image\Adapter\Gd2 $imageResize */
        if ($image && $image != 'no_selection') {
            $absolutePath = $this->fileSystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath("catalog/$dataType/") . $image;
            $writeImage = $this->fileSystem->getDirectoryWrite(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath("catalog/$dataType/resize/") . $imageWidth .  $image;

            //resize image and store it to location
            $this->resizeAndSaveImage($absolutePath, $writeImage, $imageWidth, $imageHeight);
            $imageUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . "catalog/$dataType/resize/" . $imageWidth .  $image;
            $thumbnail = $imageUrl;
        } else {
            $imageUrl = $this->getPlaceHolderImage("$dataType");
            $thumbnail = $imageUrl;
        }
        if (!empty($returnType) && $returnType == 'image_url') {
            return $imageUrl;
        }
        return ['thumbnail_url' => $thumbnail, 'image_url' => $imageUrl];
    }
}
