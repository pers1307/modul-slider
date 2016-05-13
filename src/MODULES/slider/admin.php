<?php
    /*
    СПИСОКОВЫЙ МОДУЛЬ, используется для создания на его основе модулей новости, статьи, faq и т.д.
     */

    $Tape = new MSTapeControl;

    header('Content-type: text/html; charset=utf-8');
    global $CONFIG;

    $Tape->loadConfig();

    /* Подготовительные работы для модуля */
    $table_name = $CONFIG['tables']['items']['db_name'];
    $key_field = $CONFIG['tables']['items']['key_field'];

    list($output_id) = $Tape->prepareLinkPath($CONFIG);

    $Tape->checkModuleIntegrity();

    /* Начало работы модуля - действия, реакции */
    switch (MSCore::urls()->vars[1])
    {
        case 'config':
        {
            $config = MSCore::modules()->by_dir(MSCore::urls()->vars[0]);

            $config['config'] = array();

            foreach (MSCore::page()->allZones as $_zone)
            {
                $config['config']['mod_' . $_zone['value']] = array(
                    'caption' => $_zone['value'],
                    'value' => (isset($config['output'][$_zone['value']]) ? $config['output'][$_zone['value']] : ''),
                    'module' => MSCore::urls()->vars[0],
                    'zone' => $_zone['value'],
                    'type' => 'explorer',
                );
            }

            $vars['_FORM_'] = MSCore::forms()->make($config['config']);
            $vars['mod'] = MSCore::urls()->vars[0];

            die (template('module_config', $vars));
        }
            break;

        case 'swap':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $page = isset(MSCore::urls()->vars[3]) && is_numeric(MSCore::urls()->vars[3]) ? MSCore::urls()->vars[3] : 0;
            $item_id = isset(MSCore::urls()->vars[4]) && is_numeric(MSCore::urls()->vars[4]) ? MSCore::urls()->vars[4] : 0;
            $action = (isset(MSCore::urls()->vars[5]) && MSCore::urls()->vars[5] == 'up') ? 1 : 0;

            $Tape->setSwapItemsOrder($table_name, $item_id, $action);
            $vars = $Tape->generateVars();

            $_RESULT = array('content' => template('moduleTape/fast', $vars));
            die(json_encode($_RESULT));
        }
            break;

        case 'filter':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $page = isset(MSCore::urls()->vars[3]) && is_numeric(MSCore::urls()->vars[3]) ? MSCore::urls()->vars[3] : 1;
            if(!empty($_REQUEST['filters']) && is_array($_REQUEST['filters'])) {
                $_REQUEST['filters'] = array_map('trim', $_REQUEST['filters']);
            }
            $_SESSION['filters'][$table_name] = serialize($_REQUEST['filters']);
            $vars = $Tape->generateVars();

            $_RESULT = array('content' => template('moduleTape/fast', $vars));
            die(json_encode($_RESULT));
        }
            break;

        case 'clear_filter':
            $Tape->clearFilters();

        case 'fastview':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $page = isset(MSCore::urls()->vars[3]) && is_numeric(MSCore::urls()->vars[3]) ? MSCore::urls()->vars[3] : 1;

            $vars = $Tape->generateVars();
            $_RESULT = array('content' => template('moduleTape/fast', $vars));
            die(json_encode($_RESULT));
        }
            break;

        case 'reset_order':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $art_id = isset(MSCore::urls()->vars[3]) && is_numeric(MSCore::urls()->vars[3]) ? MSCore::urls()->vars[3] : 0;

            /* ACTION */

            $Tape->resetSwapItemsOrder($table_name);
            $vars = $Tape->generateVars();
            /* ACTION */

            $_RESULT = array('content' => template('moduleTape/fast', $vars));
            die(json_encode($_RESULT));
        }
            break;

        case 'add':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $page = (isset(MSCore::urls()->vars[3])) ? (int)MSCore::urls()->vars[3] : 0;
            $new_item_id = (isset(MSCore::urls()->vars[4])) ? (int)MSCore::urls()->vars[4] : 0;

            /* ACTION */
            $OUT_CONFIG = $CONFIG;
            $CONFIG = $Tape->generateConfigValues($new_item_id);

            if (isset($_REQUEST['conf']))
            {

                //защита от дублирования поля code {start}
                if(!empty($_REQUEST['conf'][1]['code'])) {
                    $id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0;
                    $tableName = $CONFIG['tables']['items']['db_name'];
                    $codeItem = $_REQUEST['conf'][1]['code'];
                    if (MSCore::db()->getOne('SELECT `id` FROM `' . PRFX . $tableName . '` WHERE' . ($id != 0 ? ' `id`!=' . $id . ' AND' : '') . ' `path_id` = "' . getInt($path_id) . '" AND `code` = "' . MSCore::db()->pre($codeItem) . '"')) {
                        $_REQUEST['conf'][1]['code'] = $codeItem . '-' . date('dHis');
                    }
                }
                //защита от дублирования поля code {end}

                if ($inserted_id = $Tape->saveItem(false))
                {
                    $vars = $Tape->generateVars();
                    $vars['apply'] = isset(MSCore::urls()->vars[5]) && MSCore::urls()->vars[5] > 0 ? 1 : 0;

                    $inserted_id = '<input id="inserted_id" type="hidden" value="' . $inserted_id . '" name="id"/>';

                    $_RESULT = array('content' => array(template('moduleTape/fast', $vars), $inserted_id));
                    die(json_encode($_RESULT));
                }
                else
                {
                    echo '<i style="display:none">Fatal error: </i>Введенный "Символьный код" уже занят';

                    /**
                     * TODO: Сейчас на все ошибки одна причина, исправить :)
                     */
                }

            }
            else
            {
                $vars['CONFIG'] = $CONFIG;
                $vars[$key_field] = (int)$new_item_id;
                $vars['path_id'] = (int)$path_id;
                $vars['page'] = $page;
                $vars['output_id'] = $output_id;

                $vars['_FORM_'] = MSCore::forms()->make($CONFIG['tables']['items']['config']);

                echo template('moduleTape/add', $vars);
            }
            /* ACTION */

            die();
        }
            break;

        case 'delete':
        {
            $path_id = (isset(MSCore::urls()->vars[2])) ? (int)MSCore::urls()->vars[2] : 0;
            $page = (isset(MSCore::urls()->vars[3])) ? (int)MSCore::urls()->vars[3] : 0;
            $id = (isset(MSCore::urls()->vars[4])) ? (int)MSCore::urls()->vars[4] : 0;

            /* ACTION */
            if ($id > 0)
            {

                MSCore::db()->execute("DELETE FROM `" . PRFX . $table_name . "` WHERE `" . $key_field . "`=" . $id);
                $vars = $Tape->generateVars();
                $_RESULT = array('content' => template('moduleTape/fast', $vars));
                die(json_encode($_RESULT));
            }
            /* ACTION */

            exit;
        }
            break;
    }

    die();