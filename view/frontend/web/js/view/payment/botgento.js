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
            refId: config.ref_id,
            repId: config.recipientId,
            fb_state: ko.observable('unchecked'),
            send_order_cnf: config.send_order_cnf,
            origin: config.origin,
            app_id: config.app_id,
            page_id: config.page_id,
            fbShow: false,
            isVisible: ko.observable(false),
            loaderImg: ko.observable(require.toUrl('')+'Botgento_Base/images/fb-loader.gif'),
            gifVisible: ko.observable(true),
            subscribed: config.subscribed,
            initialize: function () {
                this._super();
                var self = this;

                self.isVisible(true);
                self.gifVisible(true);

                var storage = registry.get('localStorage');
                storage.set('subscribed', config.subscribed);
                // For default checkout steps
                steps.steps.subscribe(function (index) {
                    if (index.length > 1) {
                        var step = index[1]; // For Payment step
                        if (step) {
                            step.isVisible.subscribe(function (value) {
                                if (value === true && self.fbShow === false) {
                                    setTimeout(function () {
                                        self.fbStart(); // Initialize
                                    }, 1000);
                                    self.fbShow = true;
                                }
                            });
                        }
                    } else {
                        if (self.fbShow === false) {
                            setTimeout(function () {
                                self.fbStart();
                            }, 1000);
                            self.fbShow = true;
                        }
                    }
                });
            },

            fbInit: function () {
                var self = this;
                FB.init({
                    appId: self.app_id,
                    autoLogAppEvents: true,
                    xfbml: true,
                    version: 'v2.10'
                });
            },

            fbEvent: function () {
                var self = this;
                var storage = registry.get('localStorage');
                FB.Event.subscribe('messenger_checkbox', function (e) {
                    //console.log("messenger_checkbox event");
                    //console.log(e);
                    if (e.event == 'rendered') {
                        //console.log("Plugin was rendered");
                        self.isVisible(true);
                        self.gifVisible(false);
                    } else if (e.event == 'checkbox') {
                        var checkboxState = e.state;
                        //console.log("Checkbox state: " + checkboxState);
                        self.fb_state(checkboxState);
                        storage.set('fb_state', checkboxState);
                        storage.set('app_id', self.app_id);
                        storage.set('page_id', self.page_id);
                        storage.set('recipient_id', e.user_ref);
                    } else if (e.event == 'not_you') {
                        //console.log("User clicked 'not you'");
                    } else if (e.event == 'hidden') {
                        self.gifVisible(false);
                        //console.log("Plugin was hidden");
                        self.isVisible(false);
                        self.fbShow = false;
                    }
                });
                self.gifVisible(false);
            },

            fbStart: function () {
                var self = this;
                var storage = registry.get('localStorage');
                if (this.status === true && self.fbShow === true) {
                    self.isVisible(true);
                    self.gifVisible(true);
                    window.fbAsyncInit = window.fbAsyncInit || function () {
                        self.isVisible(true);
                        self.gifVisible(true);
                        self.fbInit();
                        self.fbEvent();
                    };
                    jQuery(document).ready(function () {
                        jQuery.ajaxSetup({cache: true});
                        jQuery.getScript('https://connect.facebook.net/en_US/sdk.js', function () {
                            self.fbInit();
                            self.fbEvent();
                            // console.log("Inside");
                        }).complete(function () {
                            // console.log("Complete");
                            setTimeout(function () {
                                window.fbAsyncInit();
                            },1000);
                        });
                    });
                    // console.log("Outside");
                } else {
                    if (self.subscribed) {
                        storage.set('app_id', self.app_id);
                        storage.set('page_id', self.page_id);
                        storage.set('recipient_id', self.repId);
                        storage.set('fb_state', 'checked');
                        storage.set('subscribed', self.subscribed);
                        self.fb_state('checked');
                    }
                }
            }
        });
    }
);
