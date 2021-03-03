<?php

namespace grigor\rest\security;

use yii\web\ForbiddenHttpException;

class Verifier
{
    private $firstFilter;
    private $lastFilter;

    public function append(string $permissionName): Verifier
    {
        if (empty($this->firstFilter)) {
            $this->firstFilter = new PermissionFilter();
            $this->firstFilter->setData($permissionName);
            $this->lastFilter = $this->firstFilter;
            return $this;
        }
        $newPerm = new PermissionFilter();
        $newPerm->setData($permissionName);
        $this->lastFilter = $this->lastFilter->setNext($newPerm);
        return $this;
    }

    public function can(array $data = []): bool
    {
        return empty($this->firstFilter) ? false : $this->firstFilter->can($data);
    }

    public function fireForbiddenHttpExceptionIfNotPermission(array $data = []): void
    {
        if (!$this->can($data)) {
            throw new ForbiddenHttpException();
        }
    }
}