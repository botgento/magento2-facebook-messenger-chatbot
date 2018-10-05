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
class ButtonColor implements ArrayInterface
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
                'value' => 'blue',
                'label' => __('Blue')
            ],
            [
                'value' => 'white',
                'label' => __('White')
            ]
        ];

        return $options;
    }
}
