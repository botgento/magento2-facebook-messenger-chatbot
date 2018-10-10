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
 * Interface RecipientInterface
 * @package Botgento\Base\Api\Data
 */
interface RecipientInterface
{
    /**
     * Get entity id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity id
     *
     * @param $entity_id
     * @return int
     */
    public function setEntityId($entity_id);

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param $customer_id
     * @return int
     */
    public function setCustomerId($customer_id);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param $customer_email
     * @return string
     */
    public function setCustomerEmail($customer_email);

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
     * Get hex code
     *
     * @return string
     */
    public function getHexCode();

    /**
     * Set hex code
     *
     * @param $hex_code
     * @return string
     */
    public function setHexCode($hex_code);
}
