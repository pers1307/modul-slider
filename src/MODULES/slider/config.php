<?php
    $module_name = 'slider';
    $module_caption = 'Слайдер';

    $CONFIG = array(
        'module_name' => $module_name,
        'module_caption' => $module_caption,
        'fastcall' => '/' . ROOT_PLACE . '/' . $module_name . '/fastview/',
        'version' => '1.1.0.0',
        'tables' => array(

            'items' => array(

                'db_name' => $module_name,
                'dialog' => array('width' => 660, 'height' => 410),
                'key_field' => 'id',
                'order_field' => '`order` ASC',
                'onpage' => 20,
                'config' => array(

                    'title' => array(
                        'caption' => 'Заголовок',
                        'value' => '',
                        'type' => 'string',
                        'in_list' => 1,
                        'filter' => 1,
                    ),
                    'text' => array(
                        'caption' => 'Текст',
                        'value' => '',
                        'type' => 'string',
                    ),
                    'url' => array(
                        'caption' => 'Ссылка для кнопки "Подробнее"',
                        'value' => '',
                        'type' => 'string',
                    ),
                    'image' => array(
                        'caption' => 'Картинка',
                        'value' => '',
                        'type' => 'loader',
                        'thumbs' => array(
                            'min' => array(1600, 600),
                        ),
                        'settings' => array(
                            'allowed-extensions' => 'jpg,jpeg,bmp,gif,png',
                            'size-limit' => 5 * 1024 * 1024,
                            'limit' => 1,
                            'module-name' => $module_name
                        ),
                        'in_list' => 1,
                    ),
                    'active' => array(
                        'caption' => 'Активность',
                        'value' => '1',
                        'type' => 'checkbox',
                        'in_list' => 1,
                    )
                ),
            ),
        ),
    );

    return $CONFIG;