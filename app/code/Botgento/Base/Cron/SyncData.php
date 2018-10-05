<?php
namespace Botgento\Base\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Botgento\Base\Model\SyncLogFactory;
use Botgento\Base\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SyncData
 * @package Botgento\Base\Cron
 */
class SyncData
{
    /**
     * @var SyncLogFactory
     */
    protected $syncLogFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * SyncData constructor.
     * @param SyncLogFactory $syncLogFactory
     * @param DateTime $dateTime
     * @param Data $helper
     * @param Curl $curl
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SyncLogFactory $syncLogFactory,
        DateTime $dateTime,
        Data $helper,
        Curl $curl,
        StoreManagerInterface $storeManager
    ) {
        $this->syncLogFactory = $syncLogFactory;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
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
            $storesArray = [];

            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $storesArray[] = $store->getId();
                }
            }

            $data = $this->helper->getAbandonedCartData($storesArray);

            $totalSyncCount = count($data);

            if ($totalSyncCount > 0) {
                $jsonData = ['user_data' => json_encode($data)];

                $token = $this->helper->getApiTokenByWebsiteId($websiteId);
                $websiteHash = $this->helper->getWebsiteHashByWebsiteId($websiteId);

                $url = $this->helper->getBotgentoUrl().'v1/botgento/abandoned-cart';

                $curl = $this->curl;

                $curl->addHeader('Website-Token', $token);
                $curl->addHeader('Website-Hash', $websiteHash);

                $curl->post($url, $jsonData);

                $response = $curl->getBody();

                $result = json_decode($response, true);

                $syncLogModel = $this->syncLogFactory->create();
                $syncLogModel->setType('abandoned_cart');
                $syncLogModel->setWebsiteId($websiteId);

                if (is_array($result)) {
                    if ($result['status']) {
                        $syncLogModel->setStatus('success');
                        $syncLogModel->setTotalSyncData($totalSyncCount);
                    } else {
                        $syncLogModel->setStatus('failed');
                        $syncLogModel->setErrorDetails($result['message']);
                    }
                } else {
                    $syncLogModel->setStatus('failed');
                }
                $syncLogModel->setCreatedAt($this->dateTime->gmtTimestamp());
                $syncLogModel->save();
            }
        }
    }
}
