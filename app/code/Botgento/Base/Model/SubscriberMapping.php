<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class SubscriberMapping
 * @package Botgento\Base\Model
 * @SuppressWarnings(MEQP2.PHP.ProtectedClassMember.FoundProtected)
 */
class SubscriberMapping extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */

    protected function _construct()
    {
        $this->_init('Botgento\Base\Model\ResourceModel\SubscriberMapping');
    }
}
