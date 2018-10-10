<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Model\ResourceModel\SyncAttributes;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Botgento\Base\Model\ResourceModel\SyncAttributes
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Botgento\Base\Model\SyncAttributes', 'Botgento\Base\Model\ResourceModel\SyncAttributes');
    }
}
