<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Observer;

use \Botgento\Base\Helper\Data as Helper;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveConfig
 * @package Botgento\Base
 */
class SaveConfig implements ObserverInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    protected $helper;

    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();

        $this->helper->sendStatus((int) $event->getWebsite());
    }
}
