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

use \Botgento\Base\Api\Data\RecipientInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Recipient
 * @package Botgento\Base\Model
 * @SuppressWarnings(MEQP2.PHP.ProtectedClassMember.FoundProtected)
 */
class Recipient extends AbstractModel implements RecipientInterface, IdentityInterface
{
    const RECIPIENT_ID  = 'recipient_id';
    const CACHE_TAG     = 'al_botgento_recipient';
    const CUSTOMER_ID   = 'customer_id';
    const CUSTOMER_EMAIL= 'customer_email';
    const HEX_CODE      = 'hex_code';

    protected function _construct()
    {
        $this->_init('Botgento\Base\Model\ResourceModel\Recipient');
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
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerEmail($customer_email)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customer_email);
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
        return $this->setData(self::RECIPIENT_ID, $recipient_id);
    }

    /**
     * @inheritdoc
     */
    public function getHexCode()
    {
        return $this->getData(self::HEX_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setHexCode($hex_code)
    {
        return $this->setData(self::HEX_CODE, $hex_code);
    }
}
