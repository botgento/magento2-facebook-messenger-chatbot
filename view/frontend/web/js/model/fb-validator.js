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
        'jquery',
        'uiRegistry',
        'Magento_Customer/js/model/customer',
        'mage/validation'
    ],
    function ($, registry) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var validationResult = true;
                var paymentForm = $('#co-payment-form');
                if (paymentForm.validate().errorList.length < 1) {
                    var storage = registry.get('localStorage');
                    if (storage.get('fb_state') === 'checked' &&
                        storage.get('recipient_id') &&
                        !storage.get('subscribed') && typeof FB === 'object') {
                        FB.AppEvents.logEvent('MessengerCheckboxUserConfirmation', null, {
                            'app_id':storage.get('app_id'),
                            'page_id':storage.get('page_id'),
                            'ref':'shopping-cart-company',
                            'user_ref':storage.get('recipient_id')
                        });
                    }
                    if (storage.get('subscribed') === 1) {
                        storage.set('fb_state','checked');
                    }
                }
                /*var emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                    if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }*/

                return validationResult;
            }
        };
    }
);
