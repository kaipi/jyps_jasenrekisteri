<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setPrimaryTable(array(
   'name' => 'User',
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
   'fieldName' => 'username',
   'type' => 'string',
   'scale' => 0,
   'length' => 25,
   'unique' => true,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'username',
  ));
$metadata->mapField(array(
   'fieldName' => 'salt',
   'type' => 'string',
   'scale' => 0,
   'length' => 100,
   'unique' => false,
   'nullable' => true,
   'precision' => 0,
   'columnName' => 'salt',
  ));
$metadata->mapField(array(
   'fieldName' => 'password',
   'type' => 'string',
   'scale' => 0,
   'length' => 250,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'password',
  ));
$metadata->mapField(array(
   'fieldName' => 'email',
   'type' => 'string',
   'scale' => 0,
   'length' => 60,
   'unique' => true,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'email',
  ));
$metadata->mapField(array(
   'fieldName' => 'isActive',
   'type' => 'boolean',
   'scale' => 0,
   'length' => NULL,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'is_active',
  ));
$metadata->mapField(array(
   'fieldName' => 'realname',
   'type' => 'string',
   'scale' => 0,
   'length' => 100,
   'unique' => false,
   'nullable' => false,
   'precision' => 0,
   'columnName' => 'realname',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
$metadata->mapManyToMany(array(
   'fieldName' => 'roles',
   'targetEntity' => 'JYPS\\RegisterBundle\\Entity\\Role',
   'cascade' => 
   array(
   ),
   'fetch' => 2,
   'joinTable' => 
   array(
   'name' => 'user_role',
   'joinColumns' => 
   array(
    0 => 
    array(
    'name' => 'user_id',
    'referencedColumnName' => 'id',
    'onDelete' => 'CASCADE',
    ),
   ),
   'inverseJoinColumns' => 
   array(
    0 => 
    array(
    'name' => 'role_id',
    'referencedColumnName' => 'id',
    'onDelete' => 'CASCADE',
    ),
   ),
   ),
  ));