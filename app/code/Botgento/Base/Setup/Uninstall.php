<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\WebsiteRepository;
use Magento\Framework\App\Config\Storage\Writer;
use Botgento\Base\Helper\Data as BotgentoHelper;

/**
 * Class Uninstall
 * @package Botgento\Base\Setup
 */
class Uninstall implements UninstallInterface
{
    private $websites;
    private $scope;
    public function __construct(
        Writer $scopeWriter,
        WebsiteRepository $websites
    ) {
        $this->scope = $scopeWriter;
        $this->websites = $websites;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @suppress MEQP1.Performance.Loop.ModelLSD
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $context->getVersion();
        $installer = $setup->getConnection();

        $tableName1 = $installer->getTableName('al_botgento_recipient');
        $tableName2 = $installer->getTableName('al_botgento_data');

        $scope = $this->scope;

        $installer->dropTable($tableName1);
        $installer->dropTable($tableName2);

        $items = $this->websites->getList();

        foreach ($items as $item) {
            $websiteId = $item->getId();
            $scope->delete(BotgentoHelper::STATUS, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::VALID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::API_TOKEN, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::APP_ID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::PAGE_ID, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::HEX_CODE, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::FB_CHECKBOX, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::FB_BUTTON, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::SND_ORDER_CNF, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::SND_ORDER_CNF_AFTER, ScopeInterface::SCOPE_WEBSITES, $websiteId);
            $scope->delete(BotgentoHelper::SND_SHIP_CNF, ScopeInterface::SCOPE_WEBSITES, $websiteId);
        }
        $setup->endSetup();
    }
}
