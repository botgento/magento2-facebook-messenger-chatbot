<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    public $cartHelper;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    public $escaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->logger = $logger;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository);
    }

    /**
     * Add to cart action
     *
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $productId = null;
        $product = null;
        try {
            $productId = (int)$this->getRequest()->getParam('id', false);
            if ($productId) {
                $this->getRequest()->setParams(['product' => $productId]);
            }
            $params = $this->getRequest()->getParams();
            $product = $this->_initProduct();
            /** $related = $this->getRequest()->getParam('related_product'); */
            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack('');
            }
            $this->cart->addProduct($product, $params);
            /**  if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            } */

            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $product->getName()
                );
                $this->messageManager->addSuccessMessage($message);

                $cartUrl = $this->cartHelper->getCartUrl();
                return $this->goBack($cartUrl);
            }
            return $this->goBack(null, $product);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addNoticeMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->cartHelper->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->logger->critical($e);
            $url = $this->_redirect->getRedirectUrl('');
            if ($product->getId()) {
                $url = $product->getProductUrl();
            }
            return $this->goBack($url);
        }
    }
}
