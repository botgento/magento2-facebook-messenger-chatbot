/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
/* global define, bgcClass */
define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (quote, urlBuilder, storage, url, errorProcessor, customer, fullScreenLoader) {
        'use strict';

        return function (paymentData, redirectOnSuccess, messageContainer) {
            var serviceUrl,
                payload;

            redirectOnSuccess = redirectOnSuccess !== false;

            payload = {
                cartId: quote.getQuoteId(),
                paymentMethod: paymentData,
                billingAddress: quote.billingAddress(),
                comments: jQuery('[name="comment-code"]').val()
            };
            if (jQuery('#fbmessenger').length && jQuery('#user_ref').length) {
                var state = jQuery('#fbmessenger').val();
                var user_ref = jQuery('#user_ref').val();
                var subscribed = parseInt(jQuery('#subscribed').val());
                if (state === 'checked' && user_ref) {
                    if (!subscribed) {
                        bgcClass.bgcUserCheckboxConfirm('BGC_ORDER-UPDATE', user_ref)
                    }
                    payload.fbState = state;
                    payload.user_ref = user_ref;
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
                        var delete_cookie = function (name) {
                            document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                        };
                        if (window.cookieStorage.getItem('bg_utm_status') === 1) {
                            var deleteBgCookies = function () {
                                document.cookie.split(';').map(function (c) {
                                    return c.trim().split('=').map(decodeURIComponent);
                                }).reduce(function (a, b) {
                                    try {
                                        if (b[0].indexOf('bg_utm') === 0) {
                                            delete_cookie(b[0]);
                                        } else {
                                            a[b[0]] = JSON.parse(b[1]);
                                        }
                                    } catch (e) {
                                        a[b[0]] = b[1];
                                    }
                                    return a;
                                }, {});
                            };
                            deleteBgCookies();
                        }
                        delete_cookie('bg_utm_status');
                        setTimeout(function () {
                             window.location.replace(url.build('checkout/onepage/success/'));
                        }, 100);
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
