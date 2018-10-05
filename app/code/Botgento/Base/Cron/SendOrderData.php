<?php
namespace Botgento\Base\Cron;

use Botgento\Base\Model\CronLogRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SendOrderData
 * @package Botgento\Base\Cron
 */
class SendOrderData
{
    /**
     * @var \Botgento\Base\Model\ResourceModel\CronLog\CollectionFactory
     */
    public $collectionFactory;
    /**
     * @var CronLogRepository
     */
    private $cronLogRepository;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Botgento\Base\Helper\Data
     */
    public $helper;

    /**
     * SendOrderData constructor.
     * @param \Botgento\Base\Model\ResourceModel\CronLog\CollectionFactory $collectionFactory
     * @param CronLogRepository $cronLogRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param ScopeConfigInterface $scopeConfig
     * @param \Botgento\Base\Helper\Data $helper
     */
    public function __construct(
        \Botgento\Base\Model\ResourceModel\CronLog\CollectionFactory $collectionFactory,
        \Botgento\Base\Model\CronLogRepository $cronLogRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\HTTP\Client\Curl $curl,
        ScopeConfigInterface $scopeConfig,
        \Botgento\Base\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
        $this->cronLogRepository = $cronLogRepository;
        $this->dateTime = $dateTime;
        $this->curl = $curl;
        $this->helper = $helper;
    }

    /**
     * Sends Order confirmation message to customer
     *
     * @return bool|null
     */
    public function execute()
    {
        $helper = $this->helper;
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', 0);
        $websiteIds = $helper->getWebsiteIds($collection->getColumnValues('order_id'));
        $selWebsiteId = 0;
        $status = 0;
        $snd_cnf = 0;
        $hexCode = 0;
        if (!isset($websiteIds['website_id'])) {
            return false;
        }
        $currentTime = $this->dateTime->gmtTimestamp();
        foreach ($websiteIds['website_id'] as $websiteId) {
            if ($selWebsiteId != $websiteId) {
                $status = $this->scopeConfig->getValue(
                    $helper->getStatusPath(),
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
                $valid = $this->scopeConfig->getValue(
                    $helper->getValidPath(),
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
                $snd_cnf = $this->scopeConfig->getValue(
                    $helper->getSendOrderCnfPath(),
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
                $hexCode = $this->scopeConfig->getValue(
                    $helper->getHexCodePath(),
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
            }
            // check status
            if (!$status && !$snd_cnf && !$valid) {
                continue;
            }
            if ($collection->getSize() > 0) {
                $api_token = $this->scopeConfig->getValue(
                    $helper->getApiTokenPath(),
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
                $collection = $this->collectionFactory->create()
                    ->addFieldToFilter('order_id', ['in' => $websiteIds['order_ids_' . $websiteId]])
                    ->addFieldToFilter('status', 0)
                    ->addFieldToFilter('cron_exec_count', ['lt'=>5]);

                /** @var \Botgento\Base\Model\CronLog $item */
                foreach ($collection as $item) {
                    $dbTime = (int) strtotime($item->getSendTime());
                    if ($dbTime <= $currentTime) {
                        $curl = $this->curl;
                        $curl->addHeader('Authorization', $api_token);

                        $json = json_decode($item->getApiData(), true);

                        $apiurl = $this->helper->getApiUrl($hexCode, 'order-confirmation');
                        $curl->post($apiurl, $json);

                        $body = json_decode($curl->getBody(), true);

                        $execCount = empty($item->getCronExecCount())?0:$item->getCronExecCount();
                        $execCount++;
                        if ($body['code'] == 200) {
                            $item->setStatus(1);
                        } else {
                            $item->setStatus(2);
                            $item->setCronExecCount($execCount);
                            $this->cronLogRepository->save($item);
                            throw new \Magento\Framework\Exception\LocalizedException($curl->getBody());
                        }
                        $item->setCronExecCount($execCount);
                        $this->cronLogRepository->save($item);
                    }
                }
            }
        }
        return null;
    }
}
