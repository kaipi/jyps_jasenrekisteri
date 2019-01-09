<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'MemberFeeConfig',
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
   'fieldName' => 'memberfee_name',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'memberfee_name',
  ));
$metadata->mapField(array(
   'fieldName' => 'memberfee_amount',
   'type' => 'decimal',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'memberfee_amount',
  ));
$metadata->mapField(array(
   'fieldName' => 'valid_from',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'valid_from',
  ));
$metadata->mapField(array(
   'fieldName' => 'valid_to',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'valid_to',
  ));
$metadata->mapField(array(
   'fieldName' => 'member_type',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'member_type',
  ));
$metadata->mapField(array(
   'fieldName' => 'createfees',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'createfees',
  ));
$metadata->mapField(array(
   'fieldName' => 'show_on_join_form',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'show_on_join_form',
  ));
$metadata->mapField(array(
   'fieldName' => 'campaign_fee',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'campaign_fee',
  ));
$metadata->mapField(array(
   'fieldName' => 'real_membertype',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'real_membertype',
  ));
$metadata->mapField(array(
   'fieldName' => 'show_amount',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'show_amount',
  ));
$metadata->mapField(array(
   'fieldName' => 'change_allowed_to',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'change_allowed_to',
  ));
$metadata->mapField(array(
   'fieldName' => 'change_allowed_from',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'change_allowed_from',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapOneToMany(array(
   'fieldName' => 'membertypes',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Member',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => 'member_type',
   'orphanRemoval' => false,
  ));