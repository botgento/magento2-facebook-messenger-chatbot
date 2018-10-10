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
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Botgento_Base/js/model/fb-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';

        additionalValidators.registerValidator(agreementValidator);

        return Component.extend({})
    }
);
