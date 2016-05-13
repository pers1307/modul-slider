<?php

$CONFIG = MSCore::modules()->getModuleConfig($module['module_name']);
$filename = str_Replace(PRFX, '', isset($CONFIG['table']['items']['db_name']) ? $CONFIG['table']['items']['db_name'] : $module['module_name']);
$table = PRFX . $filename;
MSCore::db()->execute('DROP TABLE `' . $table . '`', false);