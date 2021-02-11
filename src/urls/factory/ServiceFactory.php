<?php

namespace grigor\rest\urls\factory;

class ServiceFactory
{
    private $service;
    private $method;
    private $serializer;

    /**
     * ServiceFactory constructor.
     * @param $service
     * @param null $serializer
     */
    public function __construct($service, $serializer = null)
    {
        $this->service = $service;
        $this->method = $this->service['method'];
        if (!empty($serializer)) {
            $this->serializer = $serializer;
        }
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