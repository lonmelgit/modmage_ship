<?php

namespace ModMage\Ship\Model;

use ModMage\Ship\Api\Data;
use ModMage\Ship\Api\WaysRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use ModMage\Ship\Model\ResourceModel\Ways as ResourceWays;
use ModMage\Ship\Model\ResourceModel\Ways\CollectionFactory as WaysCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class WaysRepository implements WaysRepositoryInterface
{
    protected $resource;
    protected $waysFactory;
    protected $dataObjectHelper;
    protected $dataObjectProcessor;
    protected $dataWaysFactory;
    private $storeManager;

    public function __construct(
        ResourceWays $resource,
        WaysFactory $waysFactory,
        Data\WaysInterfaceFactory $dataWaysFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->waysFactory = $waysFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataWaysFactory = $dataWaysFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    public function save(\ModMage\Ship\Api\Data\WaysInterface $ways)
    {
        if ($ways->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $ways->setStoreId($storeId);
        }
        try {
            $this->resource->save($ways);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the method: %1', $exception->getMessage()),
                $exception
            );
        }
        return $ways;
    }

    public function getById($id)
    {
        $ways = $this->waysFactory->create();
        $ways->load($id);
        if (!$ways->getId()) {
            throw new NoSuchEntityException(__('Method of shipping with id "%1" does not exist.', $id));
        }
        return $ways;
    }

    public function delete(\ModMage\Ship\Api\Data\WaysInterface $ways)
    {
        try {
            $this->resource->delete($ways);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the method: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
