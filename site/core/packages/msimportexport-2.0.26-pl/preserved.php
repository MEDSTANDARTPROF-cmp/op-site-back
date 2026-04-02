<?php return array (
  'cfa4562a591840bd628ff255316b9fb2' => 
  array (
    'criteria' => 
    array (
      'key' => 'msimportexport_export_default_settings',
    ),
    'object' => 
    array (
      'key' => 'msimportexport_export_default_settings',
      'value' => '{"ym_offer_type":"simple","ym_default_currency":"RUB","ym_rate_currency":"CBRF","ym_currencies":"RUB","ym_delivery_default":0,"ym_pickup_default":1,"ym_store_default":1,"ym_available_default":1,"ym_description_fields":"description,introtext","ym_desc_allowed_tags":"<p>,<strong>,<i>,<u>","ym_include_saleprice":0,"ym_only_saleprice_price":0,"ym_include_optionsprice2":0,"ym_multicategories":0,"gallery_type":"","gallery_absolute_url":1,"gallery_concatenate_images":1,"gallery_image_delimiter":",","gallery_add_images_to_archive":"","gallery_copy_image":0,"gallery_limit":0,"gallery_sortdir":"DESC","gallery_copy_image_path":"{assets_path}images\\/export\\/{task_id}\\/","gallery_attach_settings":"{\\"thumb\\":\\"small\\",\\"width\\":150}","vendors":"","price_format":[0,".",""],"price_format_no_zeros":1,"allow_price_modification":0,"multicategory_format":0,"debug":0,"export_format":"","filename":"","export_path":"","file_ttl":0,"download_lock_ttl":"","archive":0,"skip_top_lines":"","add_keys":0,"add_fields":0,"skip_field_parse":"","first_delimiter":"|","second_delimiter":"%","auto_restart_limit":1,"limit":500,"where":"","leftjoin":"","innerjoin":"","select":"","notice":"","notice_method":"email","notice_email":"","notice_status":[],"notice_template_subject":"Task ID:{$id} status: {$status_text}","notice_template_message":"{switch $status}\\r\\n    {case \'initiated\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name}) \\r\\n    {case \'completed\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n        <ul>\\r\\n        Stats:\\r\\n        {foreach $stats as $key =>$val}\\r\\n            <li>{$key}: {$val}<\\/li>\\r\\n        {\\/foreach}\\r\\n        <\\/ul>\\r\\n        Total time: {$total_time}\\r\\n    {case \'failed\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n        Stats: {$stats|print}\\r\\n    {case default}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n{\\/switch}","csv_delimiter":";","csv_enclosure":"\\"","csv_escape":"\\\\\\\\","iteration_report":3,"task_refresh_freq":2,"script_memory_limit":"","ctx":"","resources":"","published_only":1,"exclude_deleted":1}',
      'xtype' => 'textfield',
      'namespace' => 'msimportexport',
      'area' => '',
      'editedon' => '2025-03-18 11:13:27',
    ),
  ),
  '3962610a1d68b1925e1b14c4ce1c53e1' => 
  array (
    'criteria' => 
    array (
      'key' => 'msimportexport_import_default_settings',
    ),
    'object' => 
    array (
      'key' => 'msimportexport_import_default_settings',
      'value' => '{"mspr_remove_remains":0,"msopm_disable_modification":0,"msopm_remove_modification":0,"msoc_record_find_fields":"key","msoc_disable_color":0,"msoc_remove_color":0,"gallery_base_path_images":"","gallery_resize_upload_image":0,"gallery_remove_images":0,"gallery_force_update":0,"gallery_type":"","gallery_image_delimiter":",","create_article":0,"template_article":"","template_product_default":1,"published_product_default":1,"hidemenu_product_default":1,"searchable_product_default":1,"remove_links":0,"debug":0,"start_from_line":"","file":"","remove_source_file":0,"first_delimiter":"|","second_delimiter":"%","iteration_report":2,"task_refresh_freq":15,"auto_restart_limit":1,"xlsx_only_first_sheet":0,"xlsx_skip_sheet":"","csv_delimiter":";","csv_enclosure":"\\"","csv_escape":"\\\\\\\\","convert_encoding":"","source_encode":"cp1251","notice":0,"notice_method":"email","notice_email":"","notice_status":[],"notice_template_subject":"Task ID:{$id} status: {$status_text}","notice_template_message":"{switch $status}\\r\\n    {case \'initiated\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name}) \\r\\n    {case \'completed\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n        <ul>\\r\\n        Stats:\\r\\n        {foreach $stats as $key =>$val}\\r\\n            <li>{$key}: {$val}<\\/li>\\r\\n        {\\/foreach}\\r\\n        <\\/ul>\\r\\n        Total time: {$total_time}\\r\\n    {case \'failed\'}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n        Stats: {$stats|print}\\r\\n    {case default}\\r\\n        Mode:{$mode}; preset ID:{$preset_id} ({$preset_name})\\r\\n{\\/switch}","script_memory_limit":"","checking_field":"pagetitle","check_existence":1,"disable_map_generation":1,"parent_default":0,"parent_delimiter":"","skip_empty_parent":0,"skip_action":"","skip_empty_checking_field":1,"check_unique_alias":0,"create_unique_alias":0,"template_unique_alias":"","use_alias_in_search":0,"ctx":"","completion_action":"","text_format_method":"nl2br","text_format_fields":"","template_resource_default":1,"published_resource_default":1,"hidemenu_resource_default":0,"searchable_resource_default":1,"msearch2_disable_indexing":0}',
      'xtype' => 'textfield',
      'namespace' => 'msimportexport',
      'area' => '',
      'editedon' => '2025-03-18 11:13:25',
    ),
  ),
);