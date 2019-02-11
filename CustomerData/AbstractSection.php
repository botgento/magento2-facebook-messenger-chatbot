<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 10/11/18
 * Time: 1:00 PM
 */

namespace Botgento\Base\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

abstract class AbstractSection implements SectionSourceInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $botHelper;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    public $redirect;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $urlBuilder;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    public $urlRewriteCollection;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $product_repository;

    /**
     * ProductsTime constructor.
     * @param \Botgento\Base\Helper\Data $botHelper
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewriteCollection
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ProductRepository $product_repository
     */
    public function __construct(
        \Botgento\Base\Helper\Data $botHelper,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewriteCollection,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Model\ProductRepository $product_repository
    ) {
        $this->botHelper = $botHelper;
        $this->redirect = $redirect;
        $this->urlBuilder = $urlBuilder;
        $this->urlRewriteCollection = $urlRewriteCollection;
        $this->product_repository = $product_repository;
    }

    public function initProduct()
    {
        $baseUrl = $this->getUrl('');
        $baseUrl1 = $this->getHelper()->getConfigValue('web/unsecure/base_url');
        $data = [
            'request_path' => str_replace($baseUrl, '', $this->redirect->getRefererUrl()),
            'store_id' => $this->getHelper()->getStoreId()
        ];

        if ($this->redirect->getRefererUrl() == $data['request_path']) {
            $data = [
                'request_path' => str_replace($baseUrl1, '', $this->redirect->getRefererUrl()),
                'store_id' => $this->getHelper()->getStoreId()
            ];
        }
        $product_id = [];
        if (strpos($this->redirect->getRefererUrl(), '/catalog/product/view/id/') !== false) {
            $explode = array_reverse(explode('/', $this->redirect->getRefererUrl()));

            $product_id = array_map(function ($i) {
                if (is_numeric($i)) {
                    return $i;
                }
                return null;
            }, $explode);

            $product_id = array_values(array_filter($product_id));

            if ($product_id) {
                $product_id = $product_id[0];
            }
        }

        $collection = $this->urlRewriteCollection
            ->addFieldToFilter('request_path', ['eq' => $data['request_path']])
            ->addStoreFilter($data['store_id']);

        if ($collection->getSize() || is_numeric($product_id)) {
            if (empty($product_id)) {
                $product_id = $collection->getFirstItem()->getEntityId();
            }
            return $this->product_repository->getById($product_id, false, $data['store_id']);
        }
        return false;
    }

    /**
     * @return \Botgento\Base\Helper\Data
     */
    public function getHelper()
    {
        return $this->botHelper;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
