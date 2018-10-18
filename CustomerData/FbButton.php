<?php
namespace Botgento\Base\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\UrlInterface;

class FbButton implements SectionSourceInterface
{
    /**
     * @var \Botgento\Base\Helper\Data
     */
    protected $helper;
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ProductsTime constructor.
     * @param \Botgento\Base\Helper\Data $helper
     */
    public function __construct(
        \Botgento\Base\Helper\Data $helper,
        UrlInterface $urlBuilder
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

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
        ];
    }
    /**
     * @return \Botgento\Base\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
