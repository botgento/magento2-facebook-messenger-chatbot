<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */
/**
 * Copyright © 2017 Botgento. All rights reserved.
 */
namespace Botgento\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use PHPUnit\Framework\Exception;
use Botgento\Base\Helper\Data as Helper;

class Api extends AbstractHelper
{
    /**
     * @const string
     */
    const ERROR = 'error';

    /**
     * @const int
     */
    const ERROR_CODE = 400;

    /**
     * @const string
     */
    const SUCCESS = 'success';

    /**
     * @const int
     */
    const SUCCESS_CODE = 200;
    
    const TABLE_CONFIG = 'config';
    const TABLE_USERS = 'users';
    const TABLE_ORDERS = 'orders';
    const TABLE_SYNC_ATTRIBUTES = 'sync_attributes';

    /**
     * @var null
     */
    private $table = null;

    /**
     * @var int
     */
    private $limit = 10;

    /**
     * @var int
     */
    private $offset = 1;

    /**
     * @var string
     */
    private $optionsType = null;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var array
     */
    private $message = [];

    /**
     * @var array
     */
    private $result = [];

    /**
     * @var array
     */
    private $allowedTables = [
        self::TABLE_CONFIG,
        self::TABLE_USERS,
        self::TABLE_ORDERS,
        self::TABLE_SYNC_ATTRIBUTES
    ];

    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var \Botgento\Base\Model\ResourceModel\CronLog\CollectionFactory
     */
    public $cronLogFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\Recipient\CollectionFactory
     */
    public $recipientFactory;

    /**
     * @var \Botgento\Base\Model\ResourceModel\SyncAttributes\CollectionFactory
     */
    public $snycAttributesCollectionFactory;
    
    /**
     * Api constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Botgento\Base\Model\ResourceModel\CronLog\CollectionFactory $cronLogFactory,
        \Botgento\Base\Model\ResourceModel\Recipient\CollectionFactory $recipientFactory,
        \Botgento\Base\Model\ResourceModel\SyncAttributes\CollectionFactory $snycAttributesCollectionFactory,
        Helper $helper
    ) {
    
        parent::__construct($context);

        $this->resetError();
        
        $this->cronLogFactory = $cronLogFactory;
        $this->recipientFactory = $recipientFactory;
        $this->snycAttributesCollectionFactory = $snycAttributesCollectionFactory;
        $this->helper = $helper;
    }

    /**
     * @param $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param $optionsType
     */
    public function setOptionsType($optionsType)
    {
        $this->optionsType = $optionsType;
    }

    /**
     * @return string
     */
    public function getOptionsType()
    {
        return $this->optionsType;
    }

    /**
     * @param string $message
     */
    public function setError($message = '')
    {
        if ($message) {
            $this->error = true;
            $this->message[] = $message;
        }
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        if ($this->error) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function resetError()
    {
        $this->error = false;
        $this->message = [];
    }

    /**
     * @return void
     */
    public function setSuccess()
    {
        $this->resetError();
    }

    /**
     * @param array $result
     * @return void
     */
    public function setResult($result = [])
    {
        if (is_array($result) && !empty($result)) {
            $this->resetError();
            $this->result = $result;
        }
    }

    /**
     * @return array
     */
    public function getResult()
    {
        if ($this->hasError()) {
            return [
                'status' => self::ERROR,
                'code' => self::ERROR_CODE,
                'error' => implode(", ", $this->message)
            ];
        }

        return [
            'status' => self::SUCCESS,
            'code' => self::SUCCESS_CODE,
            'data' => $this->result
        ];
    }

    /**
     * @param string $table
     * @return bool
     */
    public function verifyTable($table = '')
    {
        if (!empty($table) && in_array($table, $this->allowedTables)) {
            return true;
        }
        return false;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (is_array($options)) {
            if (isset($options['table']) && $this->verifyTable($options['table'])) {
                $this->setTable($options['table']);
            } else {
                $this->setError(__("Invalid table name."));
            }

            if (isset($options['limit'])) {
                $this->setLimit($options['limit']);
            }

            if (isset($options['offset'])) {
                $this->setOffset($options['offset']);
            }

            if (isset($options['type'])) {
                $this->setOptionsType($options['type']);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function authCheck()
    {
        $result = $this->helper->authCheck();
        if ($result['status'] == 'fail') {
            $this->setError($result['message']);
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->hasError() && $this->authCheck()) {
            $this->process();
        }

        return $this->getResult();
    }

    /**
     * @return void
     */
    public function process()
    {
        if ($this->verifyTable($this->getTable())) {
            try {
                $result = [];

                switch ($this->getTable()) {
                    case self::TABLE_CONFIG:
                        $result = $this->getConfigData();
                        break;
                    case self::TABLE_USERS:
                        $result = $this->getUserData();
                        break;
                    case self::TABLE_ORDERS:
                        $result = $this->getOrderData();
                        break;
                    case self::TABLE_SYNC_ATTRIBUTES:
                        $result = $this->getSyncAttributesData();
                        break;
                    default:
                        $this->setError(__("Invalid API request"));
                        break;
                }

                if (!empty($result)) {
                    $this->setResult($result);
                }
            } catch (Exception $e) {
                $this->setError($e);
            }
        } else {
            $this->setError(__("Invalid table name."));
        }
    }

    /**
     * @return array
     */
    private function getConfigData()
    {
        $resultData = [
            Helper::STATUS => null,
            Helper::VALID => null,
            Helper::API_TOKEN => null,
            Helper::HEX_CODE => null,
            Helper::APP_ID => null,
            Helper::PAGE_ID => null,
            Helper::FB_CHECKBOX => null,
            Helper::FB_BUTTON => null,
            Helper::SND_ORDER_CNF => null,
            Helper::SND_ORDER_CNF_AFTER => null,
            Helper::SND_SHIP_CNF => null,
        ];

        foreach ($resultData as $field => $value) {
            $resultData[$field] = $this->helper->getConfigValue($field);
        }
        
        return $resultData;
    }

    /**
     * @return array
     */
    private function getUserData()
    {
        $resultData = [];

        $collection = $this->recipientFactory->create();

        $collection->setPageSize($this->getLimit());
        $collection->setCurPage($this->getOffset());

        if ($collection->getSize()) {
            foreach ($collection as $recipient) {
                $resultData[] = $recipient->getData();
            }
        }

        return $resultData;
    }

    /**
     * @return array
     */
    private function getOrderData()
    {
        $resultData = [];

        $collection = $this->cronLogFactory->create();

        $collection->setPageSize($this->getLimit());
        $collection->setCurPage($this->getOffset());

        if ($collection->getSize()) {
            foreach ($collection as $recipient) {
                $resultData[] = $recipient->getData();
            }
        }

        return $resultData;
    }

    /**
     * @return array
     */
    private function getSyncAttributesData()
    {
        $resultData = [];

        $collection = $this->snycAttributesCollectionFactory->create()
            ->addFieldToFilter('type', $this->getOptionsType());

        if ($collection->getSize()) {
            $collection->getLastItem();
            $resultData = $collection->getData();
        }

        return $resultData;
    }
}
