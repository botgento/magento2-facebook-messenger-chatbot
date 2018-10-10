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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Addquote extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    public $quoteFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * Addquote constructor.
     *
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * Add quote action
     *
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quoteId = $this->getRequest()->getParam('id');
        if ($quoteId) {
            $quote = $this->quoteFactory->create()->load($quoteId);
            if ($quote->getId() && $quote->getIsActive() == 1) {
                if ($quote->getItemsCount() > 0) {
                    $params = $this->getRequest()->getParams();
                    unset($params['id']);

                    foreach ($params as $paramName => $paramValue) {
                        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                            ->setPath($this->getRequest()->getBasePath())
                            ->setDuration(86400*7);
                        $this->cookieManager->setPublicCookie($paramName, $paramValue, $metadata);
                    }

                    $this->checkoutSession->setQuoteId($quoteId);
                    return $resultRedirect->setPath('checkout', ['_secure' => true]);
                } else {
                    $this->messageManager->addErrorMessage(__('You have no items in your shopping cart.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Requested quote does not exist'));
            }
        }
        return $resultRedirect->setPath('/');
    }
}
