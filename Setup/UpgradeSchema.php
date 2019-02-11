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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $version = $context->getVersion();
        $connection = $installer->getConnection();

        if ($installer->tableExists('botgento_base_fb_recipients')) {
            $fbRecipientsTableName = $installer->getTable('botgento_base_fb_recipients');
            $recipientTableName = $installer->getTable('al_botgento_recipient');
            $connection->renameTable($fbRecipientsTableName, $recipientTableName);
        }

        $installer->getConnection()->addColumn(
            $installer->getTable('al_botgento_data'),
            'website_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 5,
                'nullable' => true,
                'comment' => 'Website Id',
                'after' => 'recipient_id'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('al_botgento_recipient'),
            'website_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 5,
                'nullable' => true,
                'comment' => 'Website Id',
                'after' => 'customer_id'
            ]
        );

        if (version_compare($version, '1.1.0') < 0) {
            $tableName = $installer->getTable('al_botgento_data');
            $columns = [
                'cron_exec_count' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Cron Count',
                ],
            ];
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        if (version_compare($version, '1.1.1') < 0) {
            /**
             * Create table 'al_botgento_sync_attributes'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('al_botgento_sync_attributes')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sync Attributes Id'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [],
                'Type'
            )->addColumn(
                'attributes_json',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [],
                'Attributes Json'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addIndex(
                $installer->getIdxName('al_botgento_sync_attributes', ['id']),
                ['id']
            )->setComment(
                'Botgento Sync Attributes Table'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'al_botgento_sync_log'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('al_botgento_sync_log')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sync Log Id'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [],
                'Type'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                [],
                'Status'
            )->addColumn(
                'error_details',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '',
                [],
                'Error Details'
            )->addColumn(
                'total_sync_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Total Sync Data'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('al_botgento_sync_log', ['id']),
                ['id']
            )->setComment(
                'Botgento Sync Log Table'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($version, '1.1.2') < 0) {

            /**
             * Create table 'al_botgento_subscriber_quote_mapping'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('al_botgento_subscriber_quote_mapping')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Subscriber Quote Mapping Id'
            )->addColumn(
                'uuid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'UUID'
            )->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                20,
                [],
                'Quote Id'
            )->addColumn(
                'is_button_press',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'is Button Press Flag'
            )->addIndex(
                $installer->getIdxName('al_botgento_subscriber_quote_mapping', ['id']),
                ['id']
            )->setComment(
                'Botgento Subscriber Quote Mapping Table'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($version, '1.1.3') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('al_botgento_sync_log'),
                'website_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'Website Id',
                    'after' => 'type'
                ]
            );
        }

        if (version_compare($version, '1.1.4') < 0) {

            /**
             * Create table 'al_botgento_instock_alert_subscriber'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('al_botgento_instock_alert_subscriber')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Instock Alert Subscriber Id'
            )->addColumn(
                'uuid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [],
                'UUID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                20,
                [],
                'Product Id'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                5,
                [],
                'Website Id'
            )->addColumn(
                'is_notification_sent',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'Is Notification Sent Flag'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addIndex(
                $installer->getIdxName('al_botgento_instock_alert_subscriber', ['id']),
                ['id']
            )->setComment(
                'Botgento In stock Alert Table'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($version, '1.1.6') < 0) {
            $connection->addColumn(
                $installer->getTable('al_botgento_subscriber_quote_mapping'),
                'user_ref',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'User Ref'
                ]
            );
        }

        $installer->endSetup();
    }
}
