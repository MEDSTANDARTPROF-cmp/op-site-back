<?php
$xpdo_meta_map['MsieCron']= array (
  'package' => 'msimportexport',
  'version' => '1.1',
  'table' => 'msie_cron',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'preset_id' => 0,
    'schedule' => NULL,
    'description' => NULL,
    'settings' => NULL,
    'date_last_run' => 0,
    'active' => 0,
  ),
  'fieldMeta' => 
  array (
    'preset_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'schedule' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'settings' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'date_last_run' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
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
  ),
  'composites' => 
  array (
    'Task' => 
    array (
      'class' => 'MsieTask',
      'local' => 'id',
      'foreign' => 'cron_id',
      'cardinality' => 'many',
      'owner' => 'local',
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
  ),
);
