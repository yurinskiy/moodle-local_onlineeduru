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

global $CFG, $DB, $OUTPUT, $PAGE, $SITE, $USER;

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', helper::ACTION_CREATE, PARAM_ALPHA);

if (!$id && $action !== helper::ACTION_CREATE) {
    throw new \coding_exception('Указан тип отличный от создания паспорта и не указан курс!');
}

$systemcontext = $context = context_system::instance();

if (!$id) {
    $context = context_course::instance($id);
}

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
    $heading = 'Выбор курса';
    $mform = new choose_course_form(helper::get_update_passport_url($id, $action));
} else {
    $heading = 'Заполнение данных паспорта';
    $mform = new course_passport_form(helper::get_update_passport_url($id, $action), [
        'course' => get_course($id),
        'version' => helper::get_version_passport($id),
    ]);

    $data = json_decode('{"courseid":3,"institution":"8023ea55-7851-4106-bbe2-bd01d74e4c67","external_url":{},"business_version":1,"title":" \u00ae \u041f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u043e\u0441\u043d\u043e\u0432\u044b \u043f\u0440\u0438\u043a\u043b\u0430\u0434\u043d\u043e\u0439 \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0442\u0438\u043a\u0438","started_at":1702227600,"finished_at":0,"enrollment_finished_at":0,"description":"\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435 \u043a\u0443\u0440\u0441\u0430 \r\n\r\n\u041d\u0430\u0438\u043c\u0435\u043d\u043e\u0432\u0430\u043d\u0438\u0435: \u041f\u0420\u0410\u0412\u041e\u0412\u041e\u0415 \u041e\u0411\u0415\u0421\u041f\u0415\u0427\u0415\u041d\u0418\u0415 \u0418\u041d\u041d\u041e\u0412\u0410\u0426\u0418\u041e\u041d\u041d\u041e\u0419 \u0414\u0415\u042f\u0422\u0415\u041b\u042c\u041d\u041e\u0421\u0422\u0418\r\n\r\n\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435 \u0438\u043d\u0441\u0442\u0438\u0442\u0443\u0442\u0430 (\u0444\u0430\u043a\u0443\u043b\u044c\u0442\u0435\u0442\u0430), \u043a\u0430\u0444\u0435\u0434\u0440\u044b: \u0418\u0421\u0418, \u043a\u0430\u0444\u0435\u0434\u0440\u0430 \u041f\u0440\u0430\u0432\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u044f\r\n\r\n\u0428\u0438\u0444\u0440 \u0438 \u043d\u0430\u0437\u0432\u0430\u043d\u0438\u0435 \u043d\u0430\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u043a\u0438 (\u0441\u043f\u0435\u0446\u0438\u0430\u043b\u044c\u043d\u043e\u0441\u0442\u0438):27.03.05- \u0418\u043d\u043d\u043e\u0432\u0430\u0442\u0438\u043a\u0430\r\n\r\n\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435 \u0434\u0438\u0441\u0446\u0438\u043f\u043b\u0438\u043d\u044b: \u041f\u0440\u0430\u0432\u043e\u0432\u043e\u0435 \u043e\u0431\u0435\u0441\u043f\u0435\u0447\u0435\u043d\u0438\u0435 \u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u043e\u043d\u043d\u043e\u0439 \u0434\u0435\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u0438 (\u041f\u041e\u0418\u0414)\r\n\r\n\u0423\u0440\u043e\u0432\u0435\u043d\u044c \u043e\u0431\u0440\u0430\u0437\u043e\u0432\u0430\u043d\u0438\u044f: \u0431\u0430\u043b\u0430\u043a\u0430\u0432\u0440\u0438\u0430\u0442\r\n\r\n\u041a\u0443\u0440\u0441: 3\r\n\r\n\u0424\u043e\u0440\u043c\u0430 \u043e\u0431\u0443\u0447\u0435\u043d\u0438\u044f: \u043e\u0447\u043d\u0430\u044f\r\n\r\n\u041a\u043e\u043b\u0438\u0447\u0435\u0441\u0442\u0432\u043e \u0447\u0430\u0441\u043e\u0432 \u0441 \u0440\u0430\u0437\u0431\u0438\u0432\u043a\u043e\u0439 \u043f\u043e \u0432\u0438\u0434\u0430\u043c \u0437\u0430\u043d\u044f\u0442\u0438\u0439: \u043e\u0431\u0449\u0430\u044f \u0442\u0440\u0443\u0434\u043e\u0435\u043c\u043a\u043e\u0441\u0442\u044c\r\n\u0434\u0438\u0441\u0446\u0438\u043f\u043b\u0438\u043d\u044b \u0434\u043b\u044f \u043e\u0447\u043d\u043e\u0439 \u0444\u043e\u0440\u043c\u044b - 72 \u0447\u0430\u0441., \u0430\u0443\u0434\u0438\u0442\u043e\u0440\u043d\u044b\u0435 \u0437\u0430\u043d\u044f\u0442\u0438\u044f 36 \u0447\u0430\u0441. (18 \u0447\u0430\u0441.\r\n\u043b\u0435\u043a\u0446\u0438\u0438 \u0438 18 \u0447\u0430\u0441. \u043f\u0440\u0430\u043a\u0442\u0438\u0447\u0435\u0441\u043a\u0438\u0435 \u0437\u0430\u043d\u044f\u0442\u0438\u044f).\r\n\r\n\u0424\u043e\u0440\u043c\u0430 \u043a\u043e\u043d\u0442\u0440\u043e\u043b\u044f: \u0437\u0430\u0447\u0435\u0442\r\n\r\n\u0410\u043d\u043d\u043e\u0442\u0430\u0446\u0438\u044f: \u043e\u0441\u043d\u043e\u0432\u043d\u043e\u0435 \u0432\u043d\u0438\u043c\u0430\u043d\u0438\u0435 \u0432 \u043a\u0443\u0440\u0441\u0435 \u0443\u0434\u0435\u043b\u044f\u0435\u0442\u0441\u044f \u0442\u0430\u043a\u043e\u0439 \u0446\u0435\u043b\u0438, \u043a\u0430\u043a \u0444\u043e\u0440\u043c\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435\r\n\u043f\u0440\u0430\u0432\u043e\u0432\u043e\u0439 \u043a\u0443\u043b\u044c\u0442\u0443\u0440\u044b \u0431\u0430\u043a\u0430\u043b\u0430\u0432\u0440\u0430 - \u0431\u0443\u0434\u0443\u0449\u0435\u0433\u043e \u0441\u043f\u0435\u0446\u0438\u0430\u043b\u0438\u0441\u0442\u0430 \u043f\u043e \u0443\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044e \u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u044f\u043c\u0438\r\n\u0434\u043b\u044f \u043e\u0431\u0435\u0441\u043f\u0435\u0447\u0435\u043d\u0438\u044f \u0435\u0433\u043e \u043f\u0440\u043e\u0444\u0435\u0441\u0441\u0438\u043e\u043d\u0430\u043b\u044c\u043d\u043e\u0439 \u0434\u0435\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u0438. \u041f\u0435\u0440\u0435\u0434 \u0443\u0447\u0430\u0449\u0438\u043c\u0438\u0441\u044f \u0441\u0442\u0430\u0432\u044f\u0442\u0441\u044f\r\n\u0437\u0430\u0434\u0430\u0447\u0438: \u043f\u043e\u043b\u0443\u0447\u0438\u0442\u044c \u043f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u0437\u043d\u0430\u043d\u0438\u044f \u0432 \u0441\u0444\u0435\u0440\u0435 \u0443\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f \u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u044f\u043c\u0438, \u043d\u0430\u0443\u0447\u0438\u0442\u044c\u0441\u044f\r\n\u0441\u0430\u043c\u043e\u0441\u0442\u043e\u044f\u0442\u0435\u043b\u044c\u043d\u043e \u043e\u0440\u0438\u0435\u043d\u0442\u0438\u0440\u043e\u0432\u0430\u0442\u044c\u0441\u044f \u0432 \u0438\u0441\u0442\u043e\u0447\u043d\u0438\u043a\u0430\u0445, \u0440\u0435\u0433\u0443\u043b\u0438\u0440\u0443\u044e\u0449\u0438\u0445 \u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u043e\u043d\u043d\u0443\u044e\r\n\u0434\u0435\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u044c; \u0441\u0444\u043e\u0440\u043c\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u0441\u043f\u043e\u0441\u043e\u0431\u043d\u043e\u0441\u0442\u044c \u0430\u043d\u0430\u043b\u0438\u0437\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u043f\u0440\u043e\u0435\u043a\u0442 (\u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u044e) \u043a\u0430\u043a\r\n\u043e\u0431\u044a\u0435\u043a\u0442 \u0443\u043f\u0440\u0430\u0432\u043b\u0435\u043d\u0438\u044f; \u043f\u0440\u0438\u043c\u0435\u043d\u044f\u0442\u044c \u043f\u043e\u043b\u0443\u0447\u0435\u043d\u043d\u044b\u0435 \u043f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u0437\u043d\u0430\u043d\u0438\u044f \u0432 \u0441\u0432\u043e\u0435\u0439\r\n\u043f\u0440\u043e\u0444\u0435\u0441\u0441\u0438\u043e\u043d\u0430\u043b\u044c\u043d\u043e\u0439 \u0434\u0435\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u0438.\r\n\r\n\u041c\u043e\u0434\u0443\u043b\u0438 \u0438 \u0441\u043e\u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0435 \u0442\u0435\u043c\u044b \u043a\u0443\u0440\u0441\u0430:\r\n\r\n\u0420\u0430\u0437\u0434\u0435\u043b 1. \u041e\u0431\u0449\u0435\u043f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u0438 \u043e\u0431\u0449\u0435\u0433\u0440\u0430\u0436\u0434\u0430\u043d\u0441\u043a\u0438\u0435 \u043f\u043e\u043b\u043e\u0436\u0435\u043d\u0438\u044f \u043f\u043e \u0440\u0435\u0433\u0443\u043b\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u044e\r\n\u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u043e\u043d\u043d\u043e\u0439 \u0434\u0435\u044f\u0442\u0435\u043b\u044c\u043d\u043e\u0441\u0442\u044c\u044e.\r\n\r\n\u0420\u0430\u0437\u0434\u0435\u043b 2. \u0418\u043d\u0442\u0435\u043b\u043b\u0435\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b\u0435 \u043f\u0440\u0430\u0432\u0430 \u0432 \u0441\u0444\u0435\u0440\u0435 \u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u0439. \u041f\u043e\u0440\u044f\u0434\u043e\u043a \u0437\u0430\u0449\u0438\u0442\u044b\r\n\u0438\u043d\u043d\u043e\u0432\u0430\u0446\u0438\u043e\u043d\u043d\u044b\u0445 \u043f\u0440\u0430\u0432. \r\n\r\n\u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430: \u0438\u043d\u043d\u043e\u0432\u0430\u0442\u0438\u043a\u0430, \u043f\u0440\u0430\u0432\u043e\u0432\u0430\u044f \u043d\u043e\u0440\u043c\u0430; \u0445\u043e\u0437\u044f\u0439\u0441\u0442\u0432\u0443\u044e\u0449\u0438\u0435 \u0441\u0443\u0431\u044a\u0435\u043a\u0442\u044b, \u043f\u0440\u0430\u0432\u043e\u043d\u0430\u0440\u0443\u0448\u0435\u043d\u0438\u0435;\r\n\u044e\u0440\u0438\u0434\u0438\u0447\u0435\u0441\u043a\u0430\u044f \u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0441\u0442\u044c; \u043d\u043e\u0440\u043c\u0430\u0442\u0438\u0432\u043d\u043e-\u043f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u0430\u043a\u0442\u044b; \u0438\u0441\u0442\u043e\u0447\u043d\u0438\u043a\u0438 \u043f\u0440\u0430\u0432\u0430;\r\n\u0437\u0430\u043a\u043e\u043d\u043d\u043e\u0441\u0442\u044c; \u043f\u0440\u0430\u0432\u043e\u043f\u043e\u0440\u044f\u0434\u043e\u043a;  \u0433\u0440\u0430\u0436\u0434\u0430\u043d\u0441\u043a\u043e\u0435 \u043f\u0440\u0430\u0432\u043e; \u0433\u0440\u0430\u0436\u0434\u0430\u043d\u0441\u043a\u043e-\u043f\u0440\u0430\u0432\u043e\u0432\u044b\u0435 \u0441\u0434\u0435\u043b\u043a\u0438 \u0438\r\n\u0434\u043e\u0433\u043e\u0432\u043e\u0440\u044b; \u0438\u043d\u0442\u0435\u043b\u043b\u0435\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u0430\u044f \u0441\u043e\u0431\u0441\u0442\u0432\u0435\u043d\u043d\u043e\u0441\u0442\u044c; \u0438\u043d\u0442\u0435\u043b\u043b\u0435\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b\u0435 \u043f\u0440\u0430\u0432\u0430; \u043e\u0431\u044a\u0435\u043a\u0442\u044b\r\n\u0438\u043d\u0442\u0435\u043b\u043b\u0435\u043a\u0442\u0443\u0430\u043b\u044c\u043d\u044b\u0445 \u043f\u0440\u0430\u0432; \u0438\u0441\u043a\u043e\u0432\u043e\u0435 \u043f\u0440\u043e\u0438\u0437\u0432\u043e\u0434\u0441\u0442\u0432\u043e; \u0430\u0440\u0431\u0438\u0442\u0440\u0430\u0436\u043d\u044b\u0439 \u043f\u0440\u043e\u0446\u0435\u0441\u0441\r\n\r\n\r\n\r\n\u0410\u0432\u0442\u043e\u0440: \u0424\u0430\u0440\u0430\u0444\u043e\u043d\u0442\u043e\u0432\u0430 \u0415.\u041b. \u2013 \u0441\u0442. \u043f\u0440\u0435\u043f\u043e\u0434\u0430\u0432\u0430\u0442\u0435\u043b\u044c \u043a\u0430\u0444\u0435\u0434\u0440\u044b \u041f\u0440\u0430\u0432\u043e\u0432\u0435\u0434\u0435\u043d\u0438\u044f.\r\n\r\n\u0414\u043e\u0441\u0442\u0443\u043f\u043d\u043e\u0441\u0442\u044c: \u0434\u043e\u0441\u0442\u0443\u043f \u043e\u0442\u043a\u0440\u044b\u0442\u044b\u0439  https:\/\/dl.sibsau.ru\/course\/view.php?id=2196","image":"https:\/\/dltest.sibsau.ru\/pluginfile.php\/112\/course\/overviewfiles\/%D0%9F%D1%80%D0%B0%D0%B2%D0%BE%D0%B2%D1%8B%D0%B5%20%D0%BE%D1%81%D0%BD%D0%BE%D0%B2%D1%8B%20%D0%BF%D1%80%D0%B8%D0%BA%D0%BB%D0%B0%D0%B4%D0%BD%D0%BE%D0%B9%20%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B8%D0%BA%D0%B8.jpg","content":"\u0421\u043e\u0434\u0435\u0440\u0436\u0430\u043d\u0438\u0435 \u043e\u043d\u043b\u0430\u0439\u043d-\u043a\u0443\u0440\u0441\u0430","cert":"1","results":"\u0421\u0435\u0440\u0442\u0438\u0444\u0438\u043a\u0430\u0442 \u043e\u0431 \u043e\u043a\u043e\u043d\u0447\u0430\u043d\u0438\u0438 \u043a\u0443\u0440\u0441\u0430","competence-count":4,"competence-value":{"0":"\u041a\u043e\u043c\u043f\u0435\u0442\u0435\u043d\u0446\u0438\u044f \u21161","1":"\u041a\u043e\u043c\u043f\u0435\u0442\u0435\u043d\u0446\u0438\u044f \u21162","3":"\u041a\u043e\u043c\u043f\u0435\u0442\u0435\u043d\u0446\u0438\u044f \u21163"},"competence-delete-hidden":{"2":1},"requirement-count":2,"requirement-value":["\u0417\u043d\u0430\u043d\u0438\u0435 \u0440\u0443\u0441\u0441\u043a\u043e\u0433\u043e \u044f\u0437\u044b\u043a\u0430","\u0417\u043d\u0430\u043d\u0438\u0435 \u043e\u0441\u043d\u043e\u0432 \u043c\u0430\u0442\u0435\u043c\u0430\u0442\u0438\u043a\u0438"],"direction-count":2,"direction-value":["09.03.01","09.03.02"],"teacher-count":2,"teacher-display_name":["\u0418\u0432\u0430\u043d\u043e\u0432 \u0418\u0432\u0430\u043d \u041f\u0435\u0442\u0440\u043e\u0432\u0438\u0447","\u041f\u0435\u0442\u0440\u043e\u0432 \u0418\u0432\u0430\u043d \u041b\u0435\u043e\u043d\u0438\u0434\u043e\u0432\u0438\u0447"],"teacher-image":["https:\/\/dltest.sibsau.ru\/pluginfile.php\/112\/course\/overviewfiles\/%D0%9F%D1%80%D0%B0%D0%B2%D0%BE%D0%B2%D1%8B%D0%B5%20%D0%BE%D1%81%D0%BD%D0%BE%D0%B2%D1%8B%20%D0%BF%D1%80%D0%B8%D0%BA%D0%BB%D0%B0%D0%B4%D0%BD%D0%BE%D0%B9%20%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B8%D0%BA%D0%B8.jpg","https:\/\/dltest.sibsau.ru\/pluginfile.php\/112\/course\/overviewfiles\/%D0%9F%D1%80%D0%B0%D0%B2%D0%BE%D0%B2%D1%8B%D0%B5%20%D0%BE%D1%81%D0%BD%D0%BE%D0%B2%D1%8B%20%D0%BF%D1%80%D0%B8%D0%BA%D0%BB%D0%B0%D0%B4%D0%BD%D0%BE%D0%B9%20%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B8%D0%BA%D0%B8.jpg"],"teacher-description":["",""],"duration_value":"5","duration_code":"week","credits":"5","hours":"72","submitbutton":"\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c"}');
    $mform->set_data($data);
}

if ($mform->is_cancelled()) {
    redirect(helper::MANAGER_PATH);
}

if ($mform instanceof choose_course_form && $data = $mform->get_data()) {
    redirect(helper::get_update_passport_url($data->courseid));
}

if ($data = $mform->get_data()) {
    echo '<pre>' . var_dump($data) . '</pre>';

    $model = new \local_onlineeduru\model\passport();
    // Обязательные поля
    $model->title = $data->title;
    $model->started_at = date_format_string($data->started_at, '%Y-%m-%d');
    $model->image = $data->image;
    $model->description = $data->description;
    $model->competences = implode('\n', $data->{'competence-value'});
    $model->requirements = $data->{'requirement-value'};
    $model->content = $data->content;
    $model->external_url = course_get_url($id);
    $model->direction = $data->{'direction-value'};
    $model->duration = new \local_onlineeduru\model\course_duration();
    $model->duration->code = $data->duration_code;
    $model->duration->value = $data->duration_value;
    $model->cert = $data->cert ? 'true' : 'false';
    $model->teachers = [];

    foreach ($data->{'teacher-display_name'} as $key => $item) {
        $teacher = new \local_onlineeduru\model\teacher();
        $teacher->display_name = $data->{'teacher-display_name'}[$key];
        $teacher->image = $data->{'teacher-image'}[$key];
        $teacher->description = $data->{'teacher-description'}[$key] ?: null;

        $model->teachers[] = $teacher;
    }

    $model->results = $data->results;
    $model->business_version = helper::get_version_passport($id);
    $model->credits = $data->credits;

    // Необязательные поля
    if ($value = $data->finished_at ?? null) {
        $model->finished_at = date_format_string($value, '%Y-%m-%d');
    }
    if ($value = $data->enrollment_finished_at ?? null) {
        $model->enrollment_finished_at = date_format_string($value, '%Y-%m-%d');
    }
    $model->lectures = $data->lectures ?? null;
    $model->language = $data->language ?? 'ru';
    $model->visitors = $data->visitors ?? null;

    foreach ($data->{'transfer-institution_id'} ?? [] as $key => $item) {
        $transfer = new \local_onlineeduru\model\course_transfer();
        $transfer->institution_id = $data->{'transfer-institution_id'}[$key];
        $transfer->direction_id = $data->{'transfer-direction_id'}[$key];

        $model->transfers[] = $transfer;
    }

    $model->accreditated = $data->accreditated ?? null;
    $model->hours = $data->hours ?? null;
    $model->hours_per_week = $data->hours_per_week ?? null;
    $model->promo_url = $data->promo_url ?? null;
    $model->promo_lang = $data->promo_lang ?? null;
    $model->subtitles_lang = $data->subtitles_lang ?? null;
    $model->estimation_tools = $data->estimation_tools ?? null;
    $model->proctoring_service = $data->proctoring_service ?? null;
    $model->sessionid = $data->sessionid ?? null;
    $model->proctoring_type = $data->proctoring_type ?? null;
    $model->assessment_description = $data->assessment_description ?? null;

    switch ($action) {
        case helper::ACTION_CREATE:
            $passportid = \local_onlineeduru\services\db::createPassport($id, $model);

            local_onlineeduru\event\course_passport_created::create(['context' => $context, 'objectid' => $passportid, 'courseid' => $id])->trigger();

            redirect(helper::get_passports(), 'Паспорт создан', \core\output\notification::NOTIFY_SUCCESS);
            break;
        case helper::ACTION_UPDATE:
            $passportid = \local_onlineeduru\services\db::updatePassport($id, $model);

            local_onlineeduru\event\course_passport_updated::create(['context' => $context, 'objectid' => $passportid, 'courseid' => $id])->trigger();

            redirect(helper::get_passports(), 'Паспорт обновлен', \core\output\notification::NOTIFY_SUCCESS);
            break;
        default:
            \core\notification::add('Неизвестный тип действия',  \core\output\notification::NOTIFY_ERROR);

            echo $OUTPUT->header();
            echo $OUTPUT->heading($heading);

            $mform->display();

            break;
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading);

    $mform->display();
}

echo $OUTPUT->footer();