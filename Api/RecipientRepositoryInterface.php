<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Api;

use Botgento\Base\Api\Data\RecipientInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface RecipientRepositoryInterface
 * @package Botgento\Base\Api
 */
interface RecipientRepositoryInterface
{
    /**
     * Saves recipient
     *
     * @param RecipientInterface $page
     * @return mixed
     */
    public function save(RecipientInterface $page);

    /**
     * Gets recipient by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * Gets recipient by customer id
     *
     * @param $id
     * @return mixed
     */
    public function getByCustomerId($id);

    /**
     * Gets list of recipients
     *
     * @return mixed
     */
    public function getList();

    /**
     * Deletes recipient
     *
     * @param RecipientInterface $page
     * @return mixed
     */
    public function delete(RecipientInterface $page);

    /**
     * Deletes recipient by id
     *
     * @param $id
     * @return mixed
     */
    public function deleteById($id);
}
