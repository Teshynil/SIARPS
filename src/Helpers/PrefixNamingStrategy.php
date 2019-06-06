<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

/**
 * Description of PrefixNamingStrategy
 *
 * @author Teshynil
 */
class PrefixNamingStrategy extends UnderscoreNamingStrategy {

    protected $tablePrefix = '';
    protected $columnPrefix = '';

    public function __construct($tablePrefix, $columnPrefix) {

        $this->tablePrefix = $tablePrefix;
        $this->columnPrefix = $columnPrefix;

        parent::__construct(CASE_LOWER);
    }

    public function classToTableName($className) {
        return $this->tablePrefix . parent::classToTableName($className);
    }

    public function propertyToColumnName($propertyName, $className = null) {

        return ($propertyName != 'id' ? $this->columnPrefix : '') . parent::propertyToColumnName($propertyName, $className);
    }

    public function joinColumnName($propertyName, $className = null) {
        return $this->tablePrefix . parent::joinColumnName($propertyName, $className);
    }

    public function joinKeyColumnName($entityName, $referencedColumnName = null) {
        return $this->tablePrefix . parent::joinKeyColumnName($entityName, $referencedColumnName);
    }

}
