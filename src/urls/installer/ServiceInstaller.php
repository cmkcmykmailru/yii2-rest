<?php

namespace grigor\rest\urls\installer;

use grigor\rest\security\Verifier;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class ServiceInstaller extends BaseObject
{
    private $service;
    private $method;
    private $serializer;
    private $response;
    private $permissions;
    private $whiteList;
    private $alias;

    public function installService(string $identityService): void
    {
        $serviceMetaData = \Yii::$app->serviceMetaDataReader->readMetaData($identityService);

        $this->setService(ArrayHelper::getValue($serviceMetaData, 'service'));
        $this->setSerializer(ArrayHelper::getValue($serviceMetaData, 'serializer'));
        $this->setResponse(ArrayHelper::getValue($serviceMetaData, 'response'));
        $permissions = ArrayHelper::getValue($serviceMetaData, 'permissions');
        $this->setPermissions(empty($permissions) ? [] : $permissions);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return array|null
     */
    public function getVerifier(): ?Verifier
    {
        if ($this->whiteList) {
            $verifier = new Verifier();
            foreach ($this->permissions as $permission) {
                $verifier->append($permission);
            }
            return $verifier;
        }
        if (empty($this->permissions)) {
            return null;
        }

        $verifier = new Verifier();
        foreach ($this->permissions as $permission) {
            $verifier->append($permission);
        }
        return $verifier;
    }

    public function isEmptyStatusCode(): bool
    {
        return empty($this->response);
    }

    public function getStatusCode(): int
    {
        return $this->response;
    }

    public function createService()
    {
        return \Yii::$container->get($this->service['class']);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getSerializer(): callable
    {
        if (!\Yii::$container->has($this->serializer)) {
            try {
                return \Yii::createObject($this->serializer);
            } catch (\Exception $e) {
                throw new \RuntimeException('The specified serializer "' . $this->serializer . '" does not exist', $e->getCode(), $e);
            }
        }
        return \Yii::$container->get($this->serializer);
    }

    public function isEmptySerializer(): bool
    {
        return empty($this->serializer);
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
        $this->method = $service['method'];
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    /**
     * @param mixed $serializer
     */
    public function setSerializer($serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @param mixed $permissions
     */
    public function setPermissions($permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * @param mixed $whiteList
     */
    public function setWhiteList($whiteList): void
    {
        $this->whiteList = $whiteList;
    }

    /**
     * @param mixed $alias
     */
    public function setAlias($alias): void
    {
        $this->alias = $alias;
    }

}