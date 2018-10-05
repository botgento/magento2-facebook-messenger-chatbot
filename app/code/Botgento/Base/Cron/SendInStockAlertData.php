<?php
namespace Botgento\Base\Cron;

/**
 * Class SendInStockAlertData
 * @package Botgento\Base\Cron
 */
class SendInStockAlertData
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    public $curl;

    /**
     * @var \Botgento\Base\Model\InStockAlertFactory
     */
    public $inStockAlertFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $dateTime;

    /**
     * SendInStockAlertData constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Botgento\Base\Helper\Data $helper
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Botgento\Base\Model\InStockAlertFactory $inStockAlertFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Botgento\Base\Helper\Data $helper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Botgento\Base\Model\InStockAlertFactory $inStockAlertFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->curl = $curl;
        $this->inStockAlertFactory = $inStockAlertFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Sync Data
     *
     * @return bool|null
     */
    public function execute()
    {
        foreach ($this->storeManager->getWebsites() as $website) {
            $websiteId = $website->getId();
            $data = $this->helper->getInStockAlertData($website);

            $totalInStockAlertCount = count($data);

            if (!empty($totalInStockAlertCount)) {
                $instockAlertData = array_chunk($data, 10);

                $token = $this->helper->getApiTokenByWebsiteId($websiteId);
                $websiteHash = $this->helper->getWebsiteHashByWebsiteId($websiteId);

                $url = $this->helper->getApiUrl($websiteHash, 'back-in-stock-message');

                foreach ($instockAlertData as $instockData) {
                    $instockAlertArray = [];
                    $instockIdsArray = [];

                    foreach ($instockData as $instock) {
                        if (isset($instock['instock_ids'])) {
                            foreach ($instock['instock_ids'] as $instockId) {
                                $instockIdsArray[] = $instockId;
                            }
                        }
                        unset($instock['instock_ids']);
                        $instockAlertArray[] = $instock;
                    }

                    $jsonData = ['alert_items' => json_encode($instockAlertArray)];

                    $curl = $this->curl;

                    $curl->addHeader('Authorization', "Bearer ".$token);

                    $curl->post($url, $jsonData);

                    $response = $curl->getBody();

                    $result = json_decode($response, true);

                    if (is_array($result) && $result['status'] == 'success') {
                        foreach ($instockIdsArray as $alertId) {
                            $inStockModel = $this->inStockAlertFactory->create()->load($alertId);
                            if (!empty($inStockModel)) {
                                $inStockModel->setIsNotificationSent(1);
                                $inStockModel->setUpdatedAt($this->dateTime->gmtTimestamp());
                                $inStockModel->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
