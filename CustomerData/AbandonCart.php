<?php

namespace Botgento\Base\CustomerData;

use Botgento\Base\Helper\Data;

class AbandonCart extends AbstractSection
{
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $result = [
            'status' => false
        ];
        if ((bool) $this->getHelper()->getModuleIsEnableAndValid() === true &&
            (bool) $this->getHelper()->getConfigValue(Data::FB_CHECKBOX)) {
            $product = $this->initProduct();

            if (!$product) {
                return $result;
            } elseif ($product->isAvailable()) {
                $pageArr = [
                    'page_uri' => $this->getHelper()->getCurrentUri(),
                    'product_id' => $product->getEntityId()
                ];

                $result = [
                    'status' => $this->getHelper()->getModuleIsEnableAndValid() &&
                        $this->getHelper()->getConfig($this->getHelper()->getFbCheckboxStatusPath()),
                    'product_id' => $product->getEntityId(),
                    'origin' => $this->getHelper()->getOrigin(),
                    'page_id' => $this->getHelper()->getConfig($this->getHelper()->getPageIdPath()),
                    'app_id' => $this->getHelper()->getConfig($this->getHelper()->getAppIdPath()),
                    'user_ref' => $this->getHelper()->generateUserRef($pageArr)
                ];
            }
        }
        return $result;
    }
}
