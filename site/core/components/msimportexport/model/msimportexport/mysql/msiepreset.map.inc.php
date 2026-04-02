<?php
$xpdo_meta_map['MsiePreset']= array (
  'package' => 'msimportexport',
  'version' => '1.1',
  'table' => 'msie_preset',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'token' => NULL,
    'name' => NULL,
    'description' => NULL,
    'mode' => '0',
    'service' => NULL,
    'fields' => NULL,
    'settings' => NULL,
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'token' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => true,
      'index' => 'unique',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'mode' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '0',
      'index' => 'index',
    ),
    'service' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'fields' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'settings' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'token' => 
    array (
      'alias' => 'token',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'token' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'service' => 
    array (
      'alias' => 'service',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'service' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'mode' => 
    array (
      'alias' => 'mode',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'mode' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Cron' => 
    array (
      'class' => 'MsieCron',
      'local' => 'id',
      'foreign' => 'preset_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Task' => 
    array (
      'class' => 'MsieTask',
      'local' => 'id',
      'foreign' => 'preset_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
