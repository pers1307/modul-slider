<?php
/**
 * slider.php
 * Контроллер для отображения Слайдера на главной
 *
 * @author      Pereskokov Yurii
 * @copyright   2015 Pereskokov Yurii
 * @license     Mediasite LLC
 * @link        http://www.mediasite.ru/
 */

/**
 * Возвращает слайдер для главного меню
 */
function getSlider()
{
    $query = new MSTable('{slider}');
    $query->setFields(['title', 'text', 'url', 'image']);
    $query->setFilter('`active` = 1');
    $query->setOrder('`order` ASC');

    return $query->getItems();
}

$sliders = getSlider();

echo template('main/slider', ['sliders' => $sliders]);