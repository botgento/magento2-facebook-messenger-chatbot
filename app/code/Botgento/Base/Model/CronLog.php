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

use \Botgento\Base\Api\Data\CronLogInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

/**
 * Class CronLog
 * @package Botgento\Base\Model
 */
class CronLog extends AbstractModel implements CronLogInterface, IdentityInterface
{
    const CACHE_TAG = 'al_botgento_data';

    const ID = 'botgento_data_id';

    const APIDATA = 'api_data';

    const EMAIL = 'email';

    const RECIPIENT_ID = 'recipient_id';

    const IS_GUEST = 'is_guest';

    const STATUS = 'status';

    const SEND_TIME = 'send_time';

    const CREATED_AT = 'created_at';

    const ORDER_ID = 'order_id';

    protected function _construct()
    {
        $this->_init('Botgento\Base\Model\ResourceModel\CronLog');
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getBotgentoDataId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setBotgentoDataId($botgento_data_id)
    {
        $this->setData(self::ID, $botgento_data_id);
    }

    /**
     * @inheritdoc
     */
    public function getApiData()
    {
        return $this->getData(self::APIDATA);
    }

    /**
     * @inheritdoc
     */
    public function setApiData($api_data)
    {
        $this->setData(self::APIDATA, $api_data);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($customer_email)
    {
        $this->setData(self::EMAIL, $customer_email);
    }

    /**
     * @inheritdoc
     */
    public function getIsGuest()
    {
        return $this->getData(self::IS_GUEST);
    }

    /**
     * @inheritdoc
     */
    public function setIsGuest($is_guest)
    {
        $this->setData(self::IS_GUEST, $is_guest);
    }

    /**
     * @inheritdoc
     */
    public function getRecipientId()
    {
        return $this->getData(self::RECIPIENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRecipientId($recipient_id)
    {
        $this->setData(self::RECIPIENT_ID, $recipient_id);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function setSendTime($send_time)
    {
        $this->setData(self::SEND_TIME, $send_time);
    }

    /**
     * @inheritdoc
     */
    public function getSendTime()
    {
        return $this->getData(self::SEND_TIME);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($order_id)
    {
        $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($created_at)
    {
        $this->setData(self::CREATED_AT, $created_at);
    }
}
