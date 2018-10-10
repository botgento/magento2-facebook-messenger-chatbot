/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/storage',
], function ($, Component, ko) {
    'use strict';
    return Component.extend({
        defaults: {},
        button: ko.observable(),
        api_url : null,
        origin: null,
        page: null,
        initialize: function () {
            this._super();
            var self = this;
            var formData = new FormData();
            formData.append('type', 'fb.get-message-button');
            $.ajax({
                url: self.api_url,
                dataType:'json',
                data: {'payload': JSON.stringify({type:'fb.get-message-button', page: this.page}) + ''},
                cache: false,
                method: 'POST',
                type: 'POST', // For jQuery < 1.9,
                success: function (json) {
                    if (json === null) {
                        return;
                    }
                    if (json.code === 200 && json.status === 'success') {
                        $(self.htmlId).append(json.data.content);
                        self.button(json.data.content)
                    }
                }
            });
        }
    });
});