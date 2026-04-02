<?php
$xpdo_meta_map['MsieTask']= array (
  'package' => 'msimportexport',
  'version' => '1.1',
  'table' => 'msie_task',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'token' => NULL,
    'preset_id' => NULL,
    'cron_id' => 0,
    'pid' => 0,
    'restarted' => 0,
    'status' => 'initiated',
    'deleted' => 0,
    'label' => NULL,
    'settings' => NULL,
    'properties' => NULL,
    'priority' => 0,
    'start_time' => 0.0,
    'finish_time' => 0.0,
  ),
  'fieldMeta' => 
  array (
    'token' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'preset_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'cron_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'pid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'restarted' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'initiated',
    ),
    'deleted' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'label' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
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
    'priority' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'json',
      'null' => false,
      'default' => 0,
    ),
    'start_time' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '16,6',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
    'finish_time' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '16,6',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
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
          'null' => false,
        ),
      ),
    ),
    'preset_id' => 
    array (
      'alias' => 'preset_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'preset_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'deleted' => 
    array (
      'alias' => 'deleted',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'deleted' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Preset' => 
    array (
      'class' => 'MsiePreset',
      'local' => 'preset_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Cron' => 
    array (
      'class' => 'MsieCron',
      'local' => 'cron_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
