<?php
namespace Botgento\Base\Block\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ApiToken extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Disables Api Token in botgento configuration page
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setDisabled('disabled');
        return $element->getElementHtml();
    }
}
