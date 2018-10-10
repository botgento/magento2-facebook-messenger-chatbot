<?php
/**
 * Botgento
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customise this module for your needs.
 * Please contact us https://www.botgento.com/contact.
 *
 * @category   Botgento
 * @package    Botgento
 * @copyright  Copyright (C) 2018 Botgento Inc (https://www.botgento.com/)
 * @license    https://www.botgento.com/magento-extension-license/
 */
namespace Botgento\Base\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Authentication
 * @package Botgento\Model\Config\Source
 */
class ButtonText implements ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 'SEND_TO_MESSENGER',
                'label' => 'Send to Messenger',
            ],
            [
                'value' => 'GET_THIS_IN_MESSENGER',
                'label' => 'Get this in Messenger',
            ],
            [
                'value' => 'RECEIVE_THIS_IN_MESSENGER',
                'label' => 'Receive this in Messenger',
            ],
            [
                'value' => 'SEND_THIS_TO_ME',
                'label' => 'Send this to me',
            ],
            [
                'value' => 'GET_CUSTOMER_ASSISTANCE',
                'label' => 'Get customer assistance',
            ],
            [
                'value' => 'GET_CUSTOMER_SERVICE',
                'label' => 'Get customer service',
            ],
            [
                'value' => 'GET_SUPPORT',
                'label' => 'Get support',
            ],
            [
                'value' => 'LET_US_CHAT',
                'label' => 'Let\'s chat',
            ],
            [
                'value' => 'SEND_ME_MESSAGES',
                'label' => 'Send me messages',
            ],
            [
                'value' => 'ALERT_ME_IN_MESSENGER',
                'label' => 'Alert me in Messenger',
            ],
            [
                'value' => 'SEND_ME_UPDATES',
                'label' => 'Send me updates',
            ],
            [
                'value' => 'MESSAGE_ME',
                'label' => 'Message me',
            ],
            [
                'value' => 'LET_ME_KNOW',
                'label' => 'Let me know',
            ],
            [
                'value' => 'KEEP_ME_UPDATED',
                'label' => 'Keep me updated',
            ],
            [
                'value' => 'TELL_ME_MORE',
                'label' => 'Tell me more',
            ],
            [
                'value' => 'SUBSCRIBE_IN_MESSENGER',
                'label' => 'Subscribe in Messenger',
            ],
            [
                'value' => 'SUBSCRIBE_TO_UPDATES',
                'label' => 'Subscribe to updates',
            ],
            [
                'value' => 'GET_MESSAGES',
                'label' => 'Get messages',
            ],
            [
                'value' => 'SUBSCRIBE',
                'label' => 'Subscribe',
            ],
            [
                'value' => 'GET_STARTED_IN_MESSENGER',
                'label' => 'Get started in Messenger',
            ],
            [
                'value' => 'LEARN_MORE_IN_MESSENGER',
                'label' => 'Learn more in Messenger',
            ],
            [
                'value' => 'GET_STARTED',
                'label' => 'Get Started',
            ],
        ];

        return $options;
    }
}
