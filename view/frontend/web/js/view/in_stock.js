/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* global define, bgcUserCheckboxConfirm */
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

            $.when(customerData.reload(['in-stock'], 0))
                .done(function (data) {
                    jQuery.ajaxSetup({cache: true});

                    if (data['in-stock'].status) {
                        window.isBgcStockAlert = 1;
                        self.v(data['in-stock']);
                        $('.botgento-instock-alert').show();
                    } else {
                        self.v({origin:'',page_id:'',app_id:'',user_ref:'',cta_text:'',size:'',color:''})
                    }
                    window.rememberMStockAlert = function () {
                        $.ajax({
                            url: "<?php echo $this->getUrl('botgento/instock/alert');?>",
                            data: {
                                product_id: "<?php echo $product->getId() ?>",
                                uuid: "<?php echo $helper->getUuid() ?>"
                            },
                            success: function (xhr) {
                                // console.log(xhr);
                            }
                        });
                    }
                });
        }
    });
});
