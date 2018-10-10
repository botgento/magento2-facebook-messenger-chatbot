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

use Botgento\Base\Api\Data\CronLogInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CronLogRepositoryInterface
 * @package Botgento\Base\Api
 */
interface CronLogRepositoryInterface
{
    /**
     * Saves new cron log
     *
     * @param CronLogInterface $page
     * @return mixed
     */
    public function save(CronLogInterface $page);

    /**
     * Gets cron log by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * Gets list of cron logs
     *
     * @param SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete cron log
     *
     * @param CronLogInterface $page
     * @return mixed
     */
    public function delete(CronLogInterface $page);

    /**
     * Delete cron log by id
     *
     * @param $id
     * @return mixed
     */
    public function deleteById($id);
}
