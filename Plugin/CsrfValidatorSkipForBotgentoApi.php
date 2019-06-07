<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Plugin;

/**
 * Class CsrfValidatorSkipForBotgentoApi
 * @package Botgento\Base\Plugin
 *
 * @api
 */
class CsrfValidatorSkipForBotgentoApi
{
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        $request,
        $action
    ) {
        // Check if Botgento Api call
        if ($request->getModuleName() == 'botgento') {
            return; // Skip CSRF check
        }
        $proceed($request, $action);
    }
}
