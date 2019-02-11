/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* global define, bgcClass */
define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'jquery',
    'mage/mage',
    'mage/decorate'
], function (ko, Component, customerData, _, $) {
    'use strict';

    return Component.extend({
        v: ko.observable(false),
        /** @inheritdoc */
        initialize: function () {
            var self = this;
            this._super();
            jQuery.ajaxSetup({cache: false});

            $.when(customerData.reload(['abandon-cart'], 0))
                .done(function (data) {
                    jQuery.ajaxSetup({cache: true});

                    if (data['abandon-cart'].status) {
                        var lazyCart = _.debounce(self.confirmOptIn, 300).bind(self);

                        self.v(data['abandon-cart']);
                        $('.fb-checkbox-block').show();

                        jQuery(document).on('click', '#product_addtocart_form [type="submit"]', function () {
                                if (jQuery('#product_addtocart_form').valid()) { 
                                     lazyCart() 
                                }
                        });
                    } else {
                        self.v({origin:'',page_id:'',app_id:'',user_ref:'', class:''})
                    }
                });
        },
        confirmOptIn: function () {
            bgcClass.bgcUserCheckboxConfirm('BGC_ABDON-CART', this.v().user_ref);
        }
    });
});
