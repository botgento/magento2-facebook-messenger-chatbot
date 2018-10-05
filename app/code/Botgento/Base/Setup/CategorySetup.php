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

use Magento\Catalog\Model\CategoryFactory;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class CategorySetup
 * @package Botgento\Base\Setup
 */
class CategorySetup extends EavSetup
{
    /**
     * Category model factory
     *
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Creates category model
     *
     * @param array $data
     * @return \Magento\Catalog\Model\Category
     * @codeCoverageIgnore
     */
    public function createCategory($data = [])
    {
        return $this->categoryFactory->create($data);
    }

    /**
     * Default entities and attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'catalog_category' => [
                'entity_type_id' => 3,
                'entity_model' => 'Magento\Catalog\Model\ResourceModel\Category',
                'attribute_model' => 'Magento\Catalog\Model\ResourceModel\Eav\Attribute',
                'table' => 'catalog_category_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' => 'Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection',
                'attributes' => [
                    'shop_now' => [
                        'type' => 'int',
                        'label' => 'Shop Now',
                        'input' => 'select',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'sort_order' => 1,
                        'user_defined' => 1,
                        'required' => 0,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'Botgento',
                    ],
                ],
            ]
        ];
    }
}
