<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->customRepositoryClassName = 'JYPS\RegisterBundle\Repository\Tasks\TaskRepository';
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'columnName' => 'type',
   'fieldName' => 'type',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'create_time',
   'fieldName' => 'createTime',
   'type' => 'datetime',
  ));
$metadata->mapField(array(
   'columnName' => 'process_start_time',
   'fieldName' => 'processStartTime',
   'type' => 'datetime',
   'nullable' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'process_end_time',
   'fieldName' => 'processEndTime',
   'type' => 'datetime',
   'nullable' => true,
  ));
$metadata->mapField(array(
   'columnName' => 'queue',
   'fieldName' => 'queue',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'target',
   'fieldName' => 'target',
   'type' => 'string',
   'length' => 255,
  ));
$metadata->mapField(array(
   'columnName' => 'target_id',
   'fieldName' => 'targetId',
   'type' => 'string',
   'length' => 255,
  ));
$metadata->mapField(array(
   'columnName' => 'status',
   'fieldName' => 'status',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'params',
   'fieldName' => 'params',
   'type' => 'json',
   'nullable' => true,
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);