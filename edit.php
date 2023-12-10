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

$systemcontext = $context = context_system::instance();

/** Проверяем авторизован ли пользователь */
require_login();

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/onlineeduru:manage', $context)) {
    header('Location: ' . $CFG->wwwroot);
    die();
}

if (!$id && $action !== helper::ACTION_CREATE) {
    throw new \coding_exception('Указан тип отличный от создания паспорта и не указан курс!');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/onlineeduru/edit.php', ['id' => $id, 'action' => $action]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_onlineeduru'));
$PAGE->set_heading(format_string($SITE->fullname, true, ['context' => $systemcontext]));

if (!$id) {
    $heading = 'Выбор курса';
    $mform = new choose_course_form(helper::get_update_passport_url($id, $action));
} else {
    $heading = 'Заполнение данных паспорта';
    $mform = new course_passport_form(helper::get_update_passport_url($id, $action), [
        'course' => get_course($id),
        'version' => helper::get_version_passport($id),
    ]);
}

if ($mform->is_cancelled()) {
    redirect(helper::MANAGER_PATH);
}

if ($mform instanceof choose_course_form && $data = $mform->get_data()) {
    redirect(helper::get_update_passport_url($data->courseid));
}

if ($data = $mform->get_data()) {
    $model = new \local_onlineeduru\model\passport();
    // Обязательные поля
    $model->institution = get_config('local_onlineeduru', 'institution');
    $model->title = $data->title;
    $model->started_at = date_format_string($data->started_at, '%Y-%m-%d');
    $model->image = str_replace('dltest', 'mook', $data->image);
    $model->description = $data->description;
    $model->competences = implode('\n', $data->{'competence-value'});
    $model->requirements = $data->{'requirement-value'};
    $model->content = $data->content;
    $model->external_url = str_replace('dltest', 'mook', course_get_url($id));
    $model->direction = $data->{'direction-value'};
    $model->duration = new \local_onlineeduru\model\course_duration();
    $model->duration->code = $data->duration_code;
    $model->duration->value = $data->duration_value;
    $model->cert = $data->cert ? 'true' : 'false';
    $model->teachers = [];

    foreach ($data->{'teacher-display_name'} as $key => $item) {
        $teacher = new \local_onlineeduru\model\teacher();
        $teacher->display_name = $data->{'teacher-display_name'}[$key];
        $teacher->image = str_replace('dltest', 'mook', $data->{'teacher-image'}[$key]);
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

            local_onlineeduru\event\course_passport_created::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();

            redirect(helper::get_passports(), 'Паспорт создан', \core\output\notification::NOTIFY_SUCCESS);
            break;
        case helper::ACTION_UPDATE:
            $passportid = \local_onlineeduru\services\db::updatePassport($id, $model);

            local_onlineeduru\event\course_passport_updated::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();

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