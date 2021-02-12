<?php


namespace grigor\rest\urls\installer;


interface ServiceMetaDataReaderInterface
{
    public function readRules(): array;

    public function readMetaData(string $identityService): array;
}