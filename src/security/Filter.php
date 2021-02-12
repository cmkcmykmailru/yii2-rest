<?php

namespace grigor\rest\security;

interface Filter
{
    public function setNext(Filter $filter): Filter;

    public function can(array $data = []): bool;
}