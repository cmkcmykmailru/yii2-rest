<?php

namespace grigor\rest\controllers\action;

interface ActionContextInterface
{
    public function getParams($args): ?array;
}