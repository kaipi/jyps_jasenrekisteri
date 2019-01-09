<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'EventLog',
  ));
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'fieldName' => 'event',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'event',
  ));
$metadata->mapField(array(
   'fieldName' => 'user',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'user',
  ));
$metadata->mapField(array(
   'fieldName' => 'date',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'date',
  ));
$metadata->mapField(array(
   'fieldName' => 'member_id',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'member_id',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);