/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'mage/mage',
    'mage/decorate'
], function (ko, Component, customerData, $) {
    'use strict';

    return Component.extend({
        botgentoApi: ko.observable(false),
        changed: ko.observable(false),
        script: ko.observable(''),
        /** @inheritdoc */
        initialize: function () {
            var self = this;

            this._super();
            self.botgentoApi({status : false, bgc : '', bgc_uuid : '', setBgc : '', bgc_csrf : ''});
            $.when(customerData.reload(['botgento-api']))
                .done(function (data) {
                    self.botgentoApi(customerData.get('botgento-api')());
                    if (self.botgentoApi().status === false) {
                        return;
                    }
                    self.changed(true);
                    bgc_uuid = self.botgentoApi().bgc_uuid;
                    bgc_csrf = self.botgentoApi().bgc_csrf;
                    bgc = self.botgentoApi().bgc;
                    self.script('<script type="text/javascript" src="{websiteSDK}" async="async"></script>'.replace('{websiteSDK}', self.botgentoApi().websiteSDK));
                });
            setBgc = function () {
                if (self.botgentoApi().status === false) {
                    return false;
                }
                /*return $.ajax({
                    url: self.botgentoApi().bgc_url,
                    headers: {'Authorization': self.botgentoApi().bgc_csrf},
                    success: function (xhr) {
                        // console.log(xhr);
                    }
                });*/
            };
        }
    });
});
