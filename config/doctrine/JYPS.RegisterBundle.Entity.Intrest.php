<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'Intrest',
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
   'fieldName' => 'member_id',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'member_id',
  ));
$metadata->mapField(array(
   'fieldName' => 'intrest_id',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'intrest_id',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapOneToOne(array(
   'fieldName' => 'intrest',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Member',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => NULL,
   'inversedBy' => 'intrests',
   'joinColumns' => 
   array(
   0 => 
   array(
    'name' => 'member_id',
    'unique' => false,
    'nullable' => true,
    'onDelete' => NULL,
    'columnDefinition' => NULL,
    'referencedColumnName' => 'id',
   ),
   ),
   'orphanRemoval' => false,
  ));