<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'Role',
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
   'columnName' => 'id',
   'id' => true,
  ));
$metadata->mapField(array(
   'fieldName' => 'name',
   'type' => 'string',
   'scale' => 0,
   'length' => 30,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'name',
  ));
$metadata->mapField(array(
   'fieldName' => 'role',
   'type' => 'string',
   'scale' => 0,
   'length' => 20,
   'unique' => true,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'role',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapManyToMany(array(
   'fieldName' => 'users',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\User',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => 'roles',
   'joinTable' => 
   array(
   ),
  ));