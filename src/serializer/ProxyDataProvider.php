<?php

namespace grigor\rest\serializer;

use yii\base\BaseObject;
use yii\data\DataProviderInterface;

class ProxyDataProvider extends BaseObject implements DataProviderInterface
{
    private $realDataProvider;
    private $serializer;

    public function __construct(DataProviderInterface $realDataProvider, callable $serializer)
    {
        $this->realDataProvider = $realDataProvider;
        $this->serializer = $serializer;
        parent::__construct();
    }

    public function prepare($forcePrepare = false): void
    {
        $this->realDataProvider->prepare($forcePrepare);
    }

    public function getCount(): int
    {
        return $this->realDataProvider->getCount();
    }

    public function getTotalCount(): int
    {
        return $this->realDataProvider->getTotalCount();
    }

    public function getModels(): array
    {
        return array_map($this->serializer, $this->realDataProvider->getModels());
    }

    public function getKeys(): array
    {
        return $this->realDataProvider->getKeys();
    }

    public function getSort()
    {
        return $this->realDataProvider->getSort();
    }

    public function getPagination()
    {
        return $this->realDataProvider->getPagination();
    }
}