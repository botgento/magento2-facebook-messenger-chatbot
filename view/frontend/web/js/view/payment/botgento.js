/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

define(
    ['uiComponent', 'ko', 'uiRegistry', 'Magento_Checkout/js/model/step-navigator', 'jquery'],
    function (Component, ko, registry, steps, jQuery) {
        'use strict';
        var config = {};
        if (checkoutConfig.botgento) {
            config = checkoutConfig.botgento.config;
        } else {
            config.status = false;
        }

        return Component.extend({
            defaults: {
                template: 'Botgento_Base/payment/confirm_order'
            },
            status: config.status,
            user_ref: config.user_ref,
            fb_state: ko.observable('unchecked'),
            origin: config.origin,
            app_id: config.app_id,
            page_id: config.page_id,
            isVisible: ko.observable(false),
            loaderImg: ko.observable(require.toUrl('')+'Botgento_Base/images/fb-loader.gif'),
            subscribed: config.subscribed,
            loaded: false,
            initialize: function () {
                this._super();
                var self = this;

                self.isVisible(true);

                window.fb_check = setInterval(function () {
                    if (typeof window.FB !== "undefined" &&
                        typeof window.bgcClass.loadFbCheckboxLib == "function" && !self.loaded) {
                        self.loaded = true;
                        clearTimeout(window.fb_check);
                        setTimeout(function () {
                            //bgcClass.loadFbCheckboxLib();
                        }, 5000);
                    }
                }, 300);
                if (self.subscribed) {
                    self.user_ref = config.user_ref;
                    self.fb_state = 'checked';
                }
            }
        });
    }
);
