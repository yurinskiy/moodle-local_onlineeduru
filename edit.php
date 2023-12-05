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

use local_onlineeduru\form\choose_course_form;
use local_onlineeduru\form\course_passport_form;
use local_onlineeduru\helper;

global $CFG, $DB, $OUTPUT, $PAGE, $SITE;

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', helper::ACTION_CREATE, PARAM_ALPHA);

if (!$id && $action !== helper::ACTION_CREATE) {
    throw new \coding_exception('Указан тип отличный от создания паспорта и не указан курс!');
}

$systemcontext = $context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/onlineeduru/edit.php', ['id' => $id, 'action' => $action]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_onlineeduru'));
$PAGE->set_heading(format_string($SITE->fullname, true, ['context' => $systemcontext]));

/** Проверяем авторизован ли пользователь */
require_login();

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/onlineeduru:manage', $context)) {
    header('Location: ' . $CFG->wwwroot);
    die();
}

if (!$id) {
    $mform = new choose_course_form();
} else {
    $mform = new course_passport_form(helper::get_update_passport_url($id, $action), [
        'course' => get_course($id),
        'version' => helper::get_version_passport($id),
    ]);
}

if ($mform->is_cancelled()) {
    redirect(helper::MANAGER_PATH);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_onlineeduru'));

if ($mform instanceof choose_course_form && $data = $mform->get_data()) {
    redirect(helper::get_update_passport_url($data->courseid), 'Заполняем поле паспорта данными из курса, подождите...', 1);
} else if ($data = $mform->get_data()) {
    echo 'test';

} else {
    //$mform->set_data([]);

    // Display the form.
    $mform->display();
}

echo $OUTPUT->footer();