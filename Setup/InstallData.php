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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;

/**
 * Class UpgradeData
 * @package Botgento\Base\Setup
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            /** @var \Botgento\Base\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->installEntities();

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

            $groups = [
                'botgento' => ['name' => 'Botgento', 'sort' => 200, 'id' => null],
            ];
            foreach ($groups as $k => $groupProp) {
                $categorySetup->addAttributeGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupProp['name'],
                    $groupProp['sort']
                );
                $groups[$k]['id'] = $categorySetup->getAttributeGroupId(
                    $entityTypeId,
                    $attributeSetId,
                    $groupProp['name']
                );
            }

            $attributes = [
                'shop_now' => ['group' => 'botgento', 'sort' => 1]];

            foreach ($attributes as $attributeCode => $attributeProp) {
                $categorySetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groups[$attributeProp['group']]['id'],
                    $attributeCode,
                    $attributeProp['sort']
                );
            }
        }
    }
}
