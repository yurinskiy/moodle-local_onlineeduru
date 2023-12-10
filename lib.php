<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds module specific settings to the settings block.
 *
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function local_onlineeduru_extend_settings_navigation(settings_navigation $nav, context $context)
{
    global $PAGE;

    if (!is_siteadmin() && !has_capability('local/onlineeduru:view', $context)) {
        return;
    }

    $settingnode = $nav->find('local_onlineeduru', navigation_node::TYPE_SETTING);

    if (!$settingnode) {
        return;
    }

    $mainstr = 'Список интегрированных курсов';
    $url = new moodle_url('/local/onlineeduru/index.php');
    $mainnode = navigation_node::create(
        $mainstr,
        $url,
        navigation_node::TYPE_CUSTOM,
        'onlineeduru',
        'onlineeduru',

        new pix_icon('a/view_list_active', $mainstr)
    );

    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
        $mainnode->make_active();
    }

    $settingnode->add_node($mainnode, 'local_onlineeduru_settings');

    if (has_capability('local/onlineeduru:manager', $context)) {
        $teststr = 'Проверка подключения к API ГИС СЦОС';
        $url = new moodle_url('/local/onlineeduru/test.php');
        $testnode = navigation_node::create(
            $teststr,
            $url,
            navigation_node::TYPE_SETTING,
            'onlineeduru_test_connection',
            'onlineeduru_test_connection',
            new pix_icon('t/sendmessage', $teststr)
        );


        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $testnode->make_active();
        }

        $settingnode->add_node($testnode);

        $addstr = 'Добавление паспорта курса';
        $url = new moodle_url('/local/onlineeduru/edit.php', ['action' => \local_onlineeduru\helper::ACTION_CREATE, 'id' => $PAGE->url->get_param('id')]);

        if ($PAGE->url->compare($url, URL_MATCH_PARAMS)) {
            $addnode = navigation_node::create(
                $addstr,
                $PAGE->url,
                navigation_node::TYPE_CUSTOM,
                'onlineeduru_add_passport',
                'onlineeduru_add_passport',
                new pix_icon('t/add', $addstr)
            );
            $addnode->make_active();
            $mainnode->add_node($addnode);
        }

        $editstr = 'Редактирование паспорта курса';
        $url = new moodle_url('/local/onlineeduru/edit.php', ['action' => \local_onlineeduru\helper::ACTION_UPDATE, 'id' => $PAGE->url->get_param('id')]);
        if ($PAGE->url->compare($url, URL_MATCH_PARAMS)) {
            $editnode = navigation_node::create(
                $editstr,
                $PAGE->url,
                navigation_node::TYPE_CUSTOM,
                'onlineeduru_edit_passport',
                'onlineeduru_edit_passport',
                new pix_icon('t/edit', $editstr)
            );
            $editnode->make_active();
            $editnode->hidden = true;
            $mainnode->add_node($editnode);
        }

        $viewstr = 'Просмотр паспорта курса';
        $url = new moodle_url('/local/onlineeduru/view.php');
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $viewnode = navigation_node::create(
                $viewstr,
                $PAGE->url,
                navigation_node::TYPE_CUSTOM,
                'onlineeduru_view_passport',
                'onlineeduru_view_passport',
                new pix_icon('t/hide', $viewstr)
            );
            $viewnode->make_active();
            $viewnode->hidden = true;
            $mainnode->add_node($viewnode);
        }
    }
}

function local_onlineeduru_get_course_image_url($course)
{
    global $PAGE;
    if (!is_object($course) && is_integer($course)) {
        $course = get_course($course);
    }

    if ($course instanceof \stdClass) {
        $course = new core_course_list_element($course);
    }

    $urlImage = '';
    foreach ($course->get_course_overviewfiles() as $file) {
        $isimage = $file->is_valid_image();

        if ($isimage) {
            $urlImage = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid() ?: null,
                $file->get_filepath(),
                $file->get_filename()
            )->out();
        }
    }

    return $urlImage;
}