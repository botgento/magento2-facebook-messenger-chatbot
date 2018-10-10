/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'uiRegistry'
    ],
    function (quote, urlBuilder, storage, url, errorProcessor, customer, fullScreenLoader, registry) {
        'use strict';

        return function (paymentData, redirectOnSuccess, messageContainer) {
            var serviceUrl,
                payload;

            redirectOnSuccess = redirectOnSuccess !== false;
            var local = registry.get('localStorage');

            payload = {
                cartId: quote.getQuoteId(),
                paymentMethod: paymentData,
                billingAddress: quote.billingAddress(),
                comments: jQuery('[name="comment-code"]').val()
            };
            if (local.get('fb_state')) {
                if (local.get('fb_state') === 'checked') {
                    if (!local.get('subscribed') === true && typeof window.FB === 'object') {
                        window.FB.AppEvents.logEvent('MessengerCheckboxUserConfirmation', null, {
                            'app_id': local.get('app_id'),
                            'page_id': local.get('page_id'),
                            'ref': 'shopping-cart-company',
                            'user_ref': local.get('recipient_id')
                        });
                    }
                    payload.fbState = local.get('fb_state');
                    payload.recipientId = local.get('recipient_id');
                    local.set('fb_state', null);
                }
            }
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                    quoteId: quote.getQuoteId()
                });
                payload.email = quote.guestEmail;
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            fullScreenLoader.startLoader();

            return storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).done(
                function () {
                    if (redirectOnSuccess) {
                        window.location.replace(url.build('checkout/onepage/success/'));
                    }
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
