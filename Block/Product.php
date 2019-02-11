<?php

namespace Botgento\Base\Block;

use \Magento\Framework\View\Element\Template;
use \Botgento\Base\Helper\Data as HelperData;

class Product extends Template
{
    /**
     * @var HelperData
     */
    public $helper;

    public function __construct(
        Template\Context $context,
        HelperData $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }
}
