<?php

namespace Botgento\Base\CustomerData;

use Botgento\Base\Helper\Data;

class InStockAlert extends AbstractSection
{
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $result = [ 'status' => false ];
        if ((bool) $this->getHelper()->getModuleIsEnableAndValid() === true &&
            (bool) $this->getHelper()->getConfigValue(Data::INSTOCK_ENABLE)) {
            $product = $this->initProduct();

            if (!$product) {
                return $result;
            } elseif (!$product->isAvailable()) {
                $result = [
                    'status' => $this->getHelper()->getModuleIsEnableAndValid() &&
                        $this->getHelper()->getConfig(Data::INSTOCK_ENABLE),
                    'product_id' => $product->getEntityId(),
                    'origin' => $this->getHelper()->getOrigin(),
                    'page_id' => $this->getHelper()->getConfig($this->getHelper()->getPageIdPath()),
                    'app_id' => $this->getHelper()->getConfig($this->getHelper()->getAppIdPath()),
                    'data_ref' => 'INSTOCK_' . $this->getHelper()->genBGCValue($this->redirect->getRefererUrl()),
                    'cta_text' => $this->getHelper()->getConfig(Data::INSTOCK_BUTTON_TEXT),
                    'size' => $this->getHelper()->getConfig(Data::INSTOCK_BUTTON_SIZE),
                    'color' => $this->getHelper()->getConfig(Data::INSTOCK_BUTTON_COLOR),
                    'url' => $this->getHelper()->getConfigValue('web/unsecure/base_url') . 'botgento/instock/alert',
                    'bgc_uuid' => $this->getHelper()->getUuid(),
                ];
            }
        }
        return $result;
    }
}
