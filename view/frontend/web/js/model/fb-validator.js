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
                    if (jQuery('#fbmessenger').length && jQuery('#user_ref').length) {
                        var state = jQuery('#fbmessenger').val();
                        var user_ref = jQuery('#user_ref').val();
                        var subscribed = parseInt(jQuery('#subscribed').val());
                        if (state === 'checked' &&
                            user_ref &&
                            !subscribed) {
                            // bgcClass.bgcUserCheckboxConfirm('MessengerCheckboxUserConfirmation', user_ref);
                        }
                    }
                }

                return validationResult;
            }
        };
    }
);
