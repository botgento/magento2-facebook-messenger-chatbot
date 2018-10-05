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
 * Class Recipient
 * @package Botgento\Base\Model\ResourceModel
 */
class Recipient extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('al_botgento_recipient', 'entity_id');
    }

    /**
     * Gets Recipient Id by customer id
     *
     * @param $customer_id
     * @return string
     */
    public function getIdByCustomerId($customer_id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getTable('al_botgento_recipient'), 'entity_id')
            ->where('customer_id = :customer_id');

        $bind = [':customer_id' => (int)$customer_id];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Gets Recipient Id by customer email
     *
     * @param $customer_email
     * @return string
     */
    public function getIdByCustomerEmail($customer_email)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getTable('al_botgento_recipient'), 'entity_id')
            ->where('customer_email = :customer_email');

        $bind = [':customer_email' => (string)$customer_email];

        return $connection->fetchOne($select, $bind);
    }
}
