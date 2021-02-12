<?php

namespace grigor\rest\urls\installer;

use Yii;
use yii\base\BaseObject;

class PhpServiceMetaDataReader extends BaseObject implements ServiceMetaDataReaderInterface
{
    public $serviceDirectoryPath;
    public $rulesPath;

    /**
     * PhpServiceMetaDataReader constructor.
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->serviceDirectoryPath = Yii::getAlias(rtrim($this->serviceDirectoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
        $this->rulesPath = Yii::getAlias($this->rulesPath);
    }

    public function readMetaData(string $identityService): array
    {
        return include $this->serviceDirectoryPath . $identityService . '.php';
    }

    public function readRules(): array
    {
        return include $this->rulesPath;
    }
}