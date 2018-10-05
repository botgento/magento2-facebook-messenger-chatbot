<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class SyncLog
 * @package Botgento\Base\Model\ResourceModel
 */
class SyncLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('al_botgento_sync_log', 'id');
    }
}
