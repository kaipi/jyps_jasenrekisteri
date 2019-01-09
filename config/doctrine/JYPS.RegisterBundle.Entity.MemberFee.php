<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'MemberFee',
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
   'fieldName' => 'fee_period',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'fee_period',
  ));
$metadata->mapField(array(
   'fieldName' => 'fee_amount_with_vat',
   'type' => 'decimal',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'fee_amount_with_vat',
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
   'fieldName' => 'reference_number',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'reference_number',
  ));
$metadata->mapField(array(
   'fieldName' => 'due_date',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'due_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'paid',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'paid',
  ));
$metadata->mapField(array(
   'fieldName' => 'memo',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'memo',
  ));
$metadata->mapField(array(
   'fieldName' => 'email_sent',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'email_sent',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapOneToOne(array(
   'fieldName' => 'memberfee',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Member',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => NULL,
   'inversedBy' => 'memberfees',
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