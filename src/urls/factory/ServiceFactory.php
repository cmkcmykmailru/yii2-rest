<?php

namespace grigor\rest\urls\factory;

use grigor\rest\security\Verifier;

class ServiceFactory
{
    private $service;
    private $method;
    private $serializer;
    private $response;
    private $permissions;
    private $whiteList;

    /**
     * ServiceFactory constructor.
     * @param $service
     * @param null $serializer
     */
    public function __construct($service, $serializer = null, $response = null, $permissions = [], $whiteList = true)
    {
        $this->service = $service;
        $this->method = $this->service['method'];
        $this->serializer = $serializer;
        $this->response = $response;
        $this->permissions = $permissions;
        $this->whiteList = $whiteList;
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
}