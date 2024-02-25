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
$coursename = get_course($id)->fullname;

$PAGE->set_context($context);

// Set up the page.
$title = get_string('passport_view', 'local_onlineeduru', $coursename);
$pagetitle = $title;
$url = new moodle_url('/local/onlineeduru/view.php', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

/** @var \local_onlineeduru\output\renderer $renderer */
$output = $PAGE->get_renderer('local_onlineeduru');
echo $output->header();
echo $output->heading($pagetitle);

$passport = \local_onlineeduru\services\db::get($id);

$renderable = new \local_onlineeduru\output\passport_view_page($course, $passport);
echo $output->render($renderable);

echo $output->footer();
