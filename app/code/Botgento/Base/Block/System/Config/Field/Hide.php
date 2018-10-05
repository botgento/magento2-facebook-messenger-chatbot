<?php
namespace Botgento\Base\Block\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Hide extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(AbstractElement $element)
    {
        return '';
    }
}
