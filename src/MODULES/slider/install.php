<?php
    $CONFIG = MSCore::modules()->getModuleConfig($module['module_name']);

    MSCore::db()->moduleType = 'lenta';
    MSCore::db()->createModuleTable($CONFIG['tables']['items']);

    unset($CONFIG);
    unset($module_name);