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
 * Class CronLog
 * @package Botgento\Base\Model\ResourceModel
 */
class CronLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('al_botgento_data', 'botgento_data_id');
    }

    /**
     * Gets Cron log by order increment id
     *
     * @param $customer_id
     * @return string
     */
    public function getIdByOrderId($order_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getTable('al_botgento_data'), 'botgento_data_id')
            ->where('order_id = :order_id');

        $bind = [':order_id' => (string)$order_id];

        return $connection->fetchOne($select, $bind);
    }
}
