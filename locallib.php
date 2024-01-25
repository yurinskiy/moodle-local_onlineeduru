<?php

use local_onlineeduru\form\choose_course_form;
use local_onlineeduru\form\course_passport_form;
use local_onlineeduru\helper;

defined('MOODLE_INTERNAL') || die();

function local_onlineeduru_create_passport(?int $id = null) {
    global $OUTPUT;

    if (!$id) {
        $heading = 'Выбор курса';
        $mform = new choose_course_form(helper::get_create_passport_url());
    } else {
        $heading = 'Заполнение данных паспорта';
        $mform = new course_passport_form(helper::get_create_passport_url($id), [
            'course' => get_course($id),
            'version' => helper::get_version_passport($id),
        ]);
    }

    if ($mform->is_cancelled()) {
        redirect(helper::MANAGER_PATH);
    }

    if ($mform instanceof choose_course_form && $data = $mform->get_data()) {
        redirect(helper::get_create_passport_url($data->courseid));
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

        $passportid = \local_onlineeduru\services\db::createPassport($id, $model);

        local_onlineeduru\event\course_passport_created::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();

        redirect(helper::get_passports(), 'Паспорт создан', \core\output\notification::NOTIFY_SUCCESS);
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading($heading);

        $mform->display();
    }

    echo $OUTPUT->footer();
}

function local_onlineeduru_update_passport(?int $id = null) {
    global $OUTPUT;

    $passportdb = \local_onlineeduru\services\db::getPassport($id);

    if (null === $passportdb || $passportdb->statusresponse == 200) {
        throw new \coding_exception('Не найденный или успешно отправленный паспорт редактировать нельзя!');
    }

    $passport = json_decode($passportdb->request, true, 512, JSON_THROW_ON_ERROR);
    $version = $passport['business_version'];

    $heading = 'Изменение данных паспорта';
    $mform = new course_passport_form(helper::get_update_passport_url($id), [
        'passport' => $passport,
        'course' => get_course($passportdb->courseid),
        'version' => $version,
    ]);

    if ($mform->is_cancelled()) {
        redirect(helper::MANAGER_PATH);
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
        $model->business_version = $version;
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

        $passportid = \local_onlineeduru\services\db::updatePassport($id, $model);

        switch ($passportdb->type ?? null) {
            case helper::ACTION_CREATE:
                local_onlineeduru\event\course_passport_created::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();

                redirect(helper::get_passports(), 'Паспорт исправлен и отправлен в ГИС СЦОС', \core\output\notification::NOTIFY_SUCCESS);
                return;
            case helper::ACTION_UPDATE:
                local_onlineeduru\event\course_passport_updated::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();
                redirect(helper::get_passports(), 'Новая версия паспорта исправлена и отправлен в ГИС СЦОС', \core\output\notification::NOTIFY_SUCCESS);
                return;
            default:
                throw new \coding_exception('Неизвестная операция над паспортом!');

        }
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading($heading);

        $mform->display();
    }

    echo $OUTPUT->footer();
}


function local_onlineeduru_new_version_passport(?int $id = null) {
    global $OUTPUT;

    $gis_courseid = \local_onlineeduru\services\db::get($id)->gis_courseid;
    $passportdb = \local_onlineeduru\services\db::getPassport($id);

    if (null === $passportdb || $passportdb->statusresponse != 200 || null === $gis_courseid) {
        throw new \coding_exception('Не найденный или не отправленный успешно паспорт обновить версию нельзя!');
    }

    $passport = json_decode($passportdb->request, true, 512, JSON_THROW_ON_ERROR);

    $heading = 'Новая версия данных паспорта';
    $mform = new course_passport_form(helper::get_update_new_passport_url($id), [
        'passport' => $passport,
        'course' => get_course($passportdb->courseid),
        'version' => helper::get_version_passport($id),
    ]);

    if ($mform->is_cancelled()) {
        redirect(helper::MANAGER_PATH);
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
        $model->id = $gis_courseid;

        $passportid = \local_onlineeduru\services\db::newVersionPassport($id, $model);

        local_onlineeduru\event\course_passport_updated::create(['context' => context_course::instance($id), 'objectid' => $passportid, 'courseid' => $id])->trigger();

        redirect(helper::get_passports(), 'Выпущена новая версия', \core\output\notification::NOTIFY_SUCCESS);

    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading($heading);

        $mform->display();
    }

    echo $OUTPUT->footer();
}

