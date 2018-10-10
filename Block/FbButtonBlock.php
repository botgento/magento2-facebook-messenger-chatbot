<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class FbButtonBlock
 * @package Botgento\Base\Block
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class FbButtonBlock extends \Magento\Framework\View\Element\Template
{
    const FB_API_URL = 'botgento/v1_service/';

    const CACHE_TAG = 'BOTGENTO_FB_BUTTON';

    /**
     * Displays Facebook message button
     *
     * @return string
     */
    public function getJsLayout()
    {
        $data = json_decode(parent::getJsLayout(), true);
        $data['components']['botgento-analysis']['config'] = [
            'api_url' => $this->_urlBuilder->getBaseUrl() . self::FB_API_URL,
            'page' => $this->getRequest()->getModuleName()
                . '_' . $this->getRequest()->getControllerName()
                . '_' . $this->getRequest()->getActionName()
        ];
        return json_encode($data);
    }
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return self::CACHE_TAG;
    }
}
