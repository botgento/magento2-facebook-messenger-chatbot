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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Botgento\Base\Setup
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $context->getVersion();
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'al_botgento_recipient'
         */
        $tableName = $installer->getTable('al_botgento_recipient');
        $table = $installer->getConnection()->newTable($tableName)
        ->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'Customer Id'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Email'
        )->addColumn(
            'recipient_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Recipient Id'
        )->setComment(
            'Facebook Recipients'
        );

        $installer->getConnection()->createTable($table);

        $indexesList = $setup->getConnection()->getIndexList($tableName);
        $indexName = $setup->getIdxName($tableName, ['entity_id']);

        if (!array_key_exists(strtoupper($indexName), $indexesList)) {
            $setup->getConnection()->addIndex(
                $tableName,
                $indexName,
                ['entity_id']
            );
        }

        $table = $installer->getConnection()->newTable(
            $installer->getTable('al_botgento_data')
        )->addColumn(
            'botgento_data_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Cron Id'
        )->addColumn(
            'api_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Api Data'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Email'
        )->addColumn(
            'is_guest',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'Is Guest Customer'
        )->addColumn(
            'recipient_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'User Ref'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            5,
            [],
            'Website Id'
        )->addColumn(
            'send_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Run On'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Create At'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Order Increment Id'
        )->addIndex(
            $installer->getIdxName('al_botgento_data', ['botgento_data_id']),
            ['botgento_data_id']
        )->setComment(
            'Cron Log'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
