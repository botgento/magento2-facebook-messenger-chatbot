<?php
namespace Botgento\Base\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\UrlInterface;

class FbButton extends AbstractSection
{
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'status'    => $this->getHelper()->getModuleIsEnableAndValid(),
            'bgc'      => $this->getHelper()->genBGCValue(),
            'bgc_uuid' => $this->getHelper()->getUuid(),
            'bgc_csrf' => $this->getHelper()->getApiToken(),
            'bgc_url'   => $this->getUrl('botgento/demo/bgc'),
            'hash'      => $this->getHelper()->getWebsiteHash(),
        ];
    }
}
