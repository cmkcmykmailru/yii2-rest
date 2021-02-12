<?php

namespace grigor\rest\serializer;

use grigor\rest\urls\factory\ServiceInstaller;
use yii\rest\Serializer as BaseSerializer;

/**
 *
 * @property ServiceInstaller $serviceFactory
 */
class Serializer extends BaseSerializer
{
    public $serviceInstaller;

    protected function serializeModel($model)
    {
        if ($this->serviceInstaller->isEmptySerializer()) {
            return parent::serializeModel($model);
        }
        $serializer = $this->serviceInstaller->getSerializer();
        return $serializer($model);
    }

    protected function serializeDataProvider($dataProvider)
    {
        if ($this->serviceInstaller->isEmptySerializer()) {
            return parent::serializeDataProvider($dataProvider);
        }
        $serializer = $this->serviceInstaller->getSerializer();
        return parent::serializeDataProvider(new ProxyDataProvider($dataProvider, $serializer));
    }
}