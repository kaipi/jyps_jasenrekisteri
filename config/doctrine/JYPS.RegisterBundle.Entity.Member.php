<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'Member',
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
   'fieldName' => 'firstname',
   'type' => 'string',
   'scale' => 0,
   'length' => 30,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'firstname',
  ));
$metadata->mapField(array(
   'fieldName' => 'second_name',
   'type' => 'string',
   'scale' => 0,
   'length' => 30,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'second_name',
  ));
$metadata->mapField(array(
   'fieldName' => 'surname',
   'type' => 'string',
   'scale' => 0,
   'length' => 50,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'surname',
  ));
$metadata->mapField(array(
   'fieldName' => 'street_address',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'street_address',
  ));
$metadata->mapField(array(
   'fieldName' => 'postal_code',
   'type' => 'string',
   'scale' => 0,
   'length' => 10,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'postal_code',
  ));
$metadata->mapField(array(
   'fieldName' => 'city',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'city',
  ));
$metadata->mapField(array(
   'fieldName' => 'country',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'country',
  ));
$metadata->mapField(array(
   'fieldName' => 'email',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'email',
  ));
$metadata->mapField(array(
   'fieldName' => 'referer_person_name',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'referer_person_name',
  ));
$metadata->mapField(array(
   'fieldName' => 'magazine_preference',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'magazine_preference',
  ));
$metadata->mapField(array(
   'fieldName' => 'invoice_preference',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'invoice_preference',
  ));
$metadata->mapField(array(
   'fieldName' => 'memo',
   'type' => 'string',
   'scale' => 0,
   'length' => 255,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'memo',
  ));
$metadata->mapField(array(
   'fieldName' => 'membership_start_date',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'membership_start_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'membership_end_date',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'membership_end_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'birth_year',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'birth_year',
  ));
$metadata->mapField(array(
   'fieldName' => 'member_id',
   'type' => 'integer',
   'scale' => 0,
   'length' => NULL,
   'unique' => true,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'member_id',
  ));
$metadata->mapField(array(
   'fieldName' => 'telephone',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'telephone',
  ));
$metadata->mapField(array(
   'fieldName' => 'gender',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'gender',
  ));
$metadata->mapField(array(
   'fieldName' => 'selfcare_password',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'selfcare_password',
  ));
$metadata->mapField(array(
   'fieldName' => 'selfcare_password_salt',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'selfcare_password_salt',
  ));
$metadata->mapField(array(
   'fieldName' => 'join_form_freeword',
   'type' => 'string',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'join_form_freeword',
  ));
$metadata->mapField(array(
   'fieldName' => 'mailing_list_yleinen',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'mailing_list_yleinen',
  ));
$metadata->mapField(array(
   'fieldName' => 'reminder_sent_date',
   'type' => 'date',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'reminder_sent_date',
  ));
$metadata->mapField(array(
   'fieldName' => 'next_memberfee_paid',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'next_memberfee_paid',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapOneToMany(array(
   'fieldName' => 'memberfees',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\MemberFee',
   'cascade' => 
   array(
   0 => 'remove',
   1 => 'persist',
   ),
   'fetch' => 2,
   'mappedBy' => 'memberfee',
   'orphanRemoval' => false,
  ));
$metadata->mapOneToMany(array(
   'fieldName' => 'intrests',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Intrest',
   'cascade' => 
   array(
   0 => 'remove',
   1 => 'persist',
   ),
   'fetch' => 2,
   'mappedBy' => 'intrest',
   'orphanRemoval' => false,
  ));
$metadata->mapOneToOne(array(
   'fieldName' => 'member_type',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\MemberFeeConfig',
   'cascade' => 
   array(
   0 => 'remove',
   1 => 'persist',
   ),
   'fetch' => 2,
   'mappedBy' => NULL,
   'inversedBy' => 'membertypes',
   'joinColumns' => 
   array(
   0 => 
   array(
    'name' => 'member_type_id',
    'referencedColumnName' => 'id',
   ),
   ),
   'orphanRemoval' => false,
  ));
$metadata->mapOneToMany(array(
   'fieldName' => 'children',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Member',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => 'parent',
   'orphanRemoval' => false,
  ));
$metadata->mapOneToOne(array(
   'fieldName' => 'parent',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Member',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'mappedBy' => NULL,
   'inversedBy' => 'children',
   'joinColumns' => 
   array(
   0 => 
   array(
    'name' => 'ParentMemberId',
    'unique' => false,
    'nullable' => true,
    'onDelete' => NULL,
    'columnDefinition' => NULL,
    'referencedColumnName' => 'id',
   ),
   ),
   'orphanRemoval' => false,
  ));