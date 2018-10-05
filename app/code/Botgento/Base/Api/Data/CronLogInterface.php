<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Api\Data;

/**
 * Interface CronLogInterface
 * @package Botgento\Base\Api\Data
 */
interface CronLogInterface
{
    /**
     * Get botgento data id
     *
     * @return int
     */
    public function getBotgentoDataId();

    /**
     * Set botgento data id
     *
     * @param $botgento_data_id
     * @return int
     */
    public function setBotgentoDataId($botgento_data_id);

    /**
     * Get api data
     *
     * @return string
     */
    public function getApiData();

    /**
     * Set api data
     *
     * @param $api_data
     * @return string
     */
    public function setApiData($api_data);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set customer email
     *
     * @param $customer_email
     * @return string
     */
    public function setEmail($customer_email);

    /**
     * Get is guest customer
     *
     * @return int
     */
    public function getIsGuest();

    /**
     * Set is quest customer
     *
     * @param $is_guest
     * @return int
     */
    public function setIsGuest($is_guest);

    /**
     * Get recipient id
     *
     * @return int
     */
    public function getRecipientId();

    /**
     * Set recipient id
     *
     * @param $recipient_id
     * @return int
     */
    public function setRecipientId($recipient_id);

    /**
     * Set status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get status
     *
     * @param $status
     * @return int
     */
    public function setStatus($status);

    /**
     * Get send time
     *
     * @return string
     */
    public function getSendTime();

    /**
     * Set send time
     *
     * @param $send_time
     * @return string
     */
    public function setSendTime($send_time);

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param $order_id
     * @return int
     */
    public function setOrderId($order_id);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param $created_at
     * @return string
     */
    public function setCreatedAt($created_at);
}
