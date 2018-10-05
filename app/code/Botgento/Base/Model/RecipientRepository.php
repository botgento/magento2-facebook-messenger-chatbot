<?php
/**
 * @author Botgento Team
 * @copyright Copyright (c) 2017 Botgento (https://www.botgento.com)
 * @package Botgento_Base
 */

/**
 * Copyright © 2017 Botgento. All rights reserved.
 */

namespace Botgento\Base\Model;

use Botgento\Base\Api\RecipientRepositoryInterface;
use Botgento\Base\Api\Data\RecipientInterface;
use Botgento\Base\Model\RecipientFactory;
use Botgento\Base\Model\ResourceModel\Recipient\CollectionFactory;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class RecipientRepository
 * @package Botgento\Base\Model
 */
class RecipientRepository implements RecipientRepositoryInterface
{
    /**
     * @var \Botgento\Base\Model\RecipientFactory
     */
    private $objectFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ResourceModel\Recipient
     */
    private $recipient;

    /**
     * RecipientRepository constructor.
     * @param \Botgento\Base\Model\RecipientFactory $objectFactory
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        RecipientFactory $objectFactory,
        \Botgento\Base\Model\ResourceModel\Recipient $recipient,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->objectFactory        = $objectFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->recipient = $recipient;
    }

    /**
     * @param RecipientInterface $object
     * @return RecipientInterface|bool
     */
    public function save(RecipientInterface $object)
    {
        try {
            $object->save();
        } catch (\Exception $e) {
            return false;
        }
        return $object;
    }

    /**
     * @param $id
     * @return RecipientInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;
    }

    /**
     * @param $id
     * @return RecipientInterface|bool
     */
    public function getByCustomerId($id)
    {
        $id = $this->recipient->getIdByCustomerId($id);
        if (!$id) {
            return false;
        }
        $item = $this->objectFactory->create();
        $item->load($id);

        if (!$item->getId()) {
            return false;
        }

        return $item;
    }

    /**
     * @param $id
     * @return RecipientInterface|bool
     */
    public function getByCustomerEmail($email)
    {
        $id = $this->recipient->getIdByCustomerEmail($email);
        if (!$id) {
            return false;
        }
        $item = $this->objectFactory->create();
        $item->load($id);
        if (!$item->getId()) {
            return false;
        }
        return $item;
    }

    /**
     * @param RecipientInterface $object
     * @return bool
     */
    public function delete(RecipientInterface $object)
    {
        try {
            $object->delete();
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id)
    {
        try {
            return $this->delete($this->getById($id));
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return ResourceModel\Recipient\Collection
     */
    public function getList()
    {
        $collection = $this->collectionFactory->create();
        return $collection;
    }
}
