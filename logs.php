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
 * @copyright 2024, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $DB, $PAGE, $SITE;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

define('DEFAULT_PAGE_SIZE', 20);

$id         = optional_param('id', 0, PARAM_INT); // Which page to show.
$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.

$systemcontext = $context = context_system::instance();

/** Проверяем авторизован ли пользователь */
require_login();

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/onlineeduru:manager', $context)) {
    header('Location: ' . $CFG->wwwroot);
    die();
}

$PAGE->set_context($context);

if ($id) {
    // Set up the page.
    $title = get_string('log_by_id', 'local_onlineeduru', $id);
    $pagetitle = $title;
    $url = new moodle_url('/local/onlineeduru/logs.php', ['id' => $id]);
    $PAGE->set_url($url);
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    $PAGE->set_pagelayout('admin');

    /** @var \local_onlineeduru\output\renderer $renderer */
    $output = $PAGE->get_renderer('local_onlineeduru');
    echo $output->header();
    echo $output->heading($pagetitle);

    $log = \local_onlineeduru\services\db::log($id);
    $renderable = new \local_onlineeduru\output\log_page($log);
    echo $output->render($renderable);

    echo $output->footer();

    return;
}

// Set up the page.
$title = get_string('logs', 'local_onlineeduru');
$pagetitle = $title;
$url = new moodle_url('/local/onlineeduru/logs.php', [
    'page' => $page,
    'perpage' => $perpage
]);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

/** @var \local_onlineeduru\output\renderer $renderer */
$output = $PAGE->get_renderer('local_onlineeduru');
echo $output->header();
echo $output->heading($pagetitle);

$logs = \local_onlineeduru\services\db::logs([], $page, $perpage);

echo $output->logs_table($logs, $page, $perpage, $url);

echo $output->footer();