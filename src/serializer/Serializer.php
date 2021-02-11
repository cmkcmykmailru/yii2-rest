<?php

namespace grigor\rest\serializer;

use grigor\rest\urls\factory\ServiceFactory;
use yii\rest\Serializer as BaseSerializer;

/**
 *
 * @property ServiceFactory $serviceFactory
 */
class Serializer extends BaseSerializer
{
    public $serviceFactory;

    protected function serializeModel($model)
    {
        if ($this->serviceFactory->isEmptySerializer()) {
            return parent::serializeModel($model);
        }
        $serializer = $this->serviceFactory->getSerializer();
        return $serializer($model);
    }

    protected function serializeDataProvider($dataProvider)
    {
        if ($this->serviceFactory->isEmptySerializer()) {
            return parent::serializeDataProvider($dataProvider);
        }
        $serializer = $this->serviceFactory->getSerializer();
        return parent::serializeDataProvider(new ProxyDataProvider($dataProvider, $serializer));
    }
}