<?php

namespace grigor\rest\security;

use yii\web\ForbiddenHttpException;

class Verifier
{
    private $firstFilter;
    private $lastFilter;

    public function append(string $permissionName): Filter
    {
        if (empty($this->firstFilter)) {
            $this->firstFilter = new PermissionFilter();
            $this->firstFilter->setData($permissionName);
            $this->lastFilter = $this->firstFilter;
            return $this->lastFilter;
        }
        $newPerm = new PermissionFilter();
        $newPerm->setData($permissionName);
        $this->lastFilter = $this->lastFilter->setNext($newPerm);
        return $newPerm;
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