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

global $CFG, $DB, $OUTPUT, $PAGE, $SITE;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);

$systemcontext = $context = context_system::instance();

/** Проверяем авторизован ли пользователь */
require_login();

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/onlineeduru:view', $context)) {
    header('Location: ' . $CFG->wwwroot);
    die();
}

$course = $DB->get_record('local_onlineeduru_course', ['courseid' => $id], '*', MUST_EXIST);

$PAGE->set_context($context);
$PAGE->set_url('/local/onlineeduru/view.php', ['id' => $id]); // Defined here to avoid notices on errors etc.
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_onlineeduru'));
$PAGE->set_heading(format_string($SITE->fullname, true, ['context' => $systemcontext]));

echo $OUTPUT->header();
echo $OUTPUT->heading('Просмотр паспорта курса');

$passport = \local_onlineeduru\services\db::get($id);

echo '<pre>' . print_r($passport,1 ) . '</pre>';

//local_onlineeduru\event\course_passport_created::create(['context' => context_course::instance($id), 'objectid' => $passport->passportid, 'courseid' => $passport->courseid])->trigger();

echo $OUTPUT->footer();
