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
use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class CategorySetup
 * @package Botgento\Base\Setup
 * @uses \Magento\Catalog\Setup\CategorySetup reference to create "Shop Now" attribute in category entity
 */
class CategorySetup extends EavSetup
{
    /**
     * @var ProductMetadataInterface
     */
    public $productMetadata;
    /**
     * Category model factory
     *
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * This should be set explicitly
     */
    const CATEGORY_ENTITY_TYPE_ID = 3;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param CategoryFactory $categoryFactory
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        CategoryFactory $categoryFactory,
        ProductMetadataInterface $productMetadata
    ) {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
        $this->productMetadata = $productMetadata;
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
     */
    public function getDefaultEntities()
    {
        $data = [
            'catalog_category' => [
                'entity_type_id' => self::CATEGORY_ENTITY_TYPE_ID,
                'entity_model' => Category::class,
                'attribute_model' => Attribute::class,
                'table' => 'catalog_category_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' =>
                    Collection::class,
                'attributes' => [
                    'shop_now' => [
                        'type' => 'int',
                        'label' => 'Shop Now',
                        'input' => 'select',
                        'source' => Boolean::class,
                        'sort_order' => 1,
                        'user_defined' => 1,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'Botgento',
                    ],
                ],
            ]
        ];

        // For Magento 2.0 compatible
        if (version_compare($this->productMetadata->getVersion(), '2.0.99') < 0) {
            unset($data['catalog_category']['entity_type_id']);
        }
        return $data;
    }
}
