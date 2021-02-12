<?php

namespace grigor\rest\security;

abstract class AbstractFilter implements Filter
{
    private $next;

    public function setNext(Filter $filter): Filter
    {
        $this->next = $filter;
        return $filter;
    }

    public function can(array $data = []): bool
    {
        if ($this->next) {
            return $this->next->can($data);
        }
        return false;
    }
}