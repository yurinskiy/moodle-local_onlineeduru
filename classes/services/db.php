<?php

namespace local_onlineeduru\services;

use completion_info;
use core\event\user_enrolment_created;
use core\uuid;
use core_completion\progress;
use local_onlineeduru\helper;
use local_onlineeduru\model\passport;

class db
{
    public static function list(array $params = [], int $page = 0, int $perpage = 20): array
    {
        global $DB;

        $fields = "SELECT c.*, p.*, p.id as passportid, p.statusresponse as status, case when p.statusresponse != 200 then p.response end as error";
        $countfields = "SELECT COUNT(*)";

        $sql = " FROM {local_onlineeduru_course} c JOIN {local_onlineeduru_passport} p on p.courseid = c.courseid and p.active = 1";

        $wheresql = '';

        $total = $all = $DB->count_records_sql($countfields . $sql . $wheresql);

        if (!empty($params)) {
            $total = $DB->count_records_sql($countfields . $sql . $wheresql, $params);
        }

        $order = " ORDER BY c.id DESC";

        $records = $DB->get_records_sql($fields . $sql . $wheresql . $order, $params, $page * $perpage, $perpage);

        return [
            'all' => $all,
            'total' => $total,
            'data' => $records,
        ];
    }

    public static function get(int $courseid): ?\stdClass
    {
        global $DB;

        $fields = "SELECT c.*, p.*, p.id as passportid";

        $sql = " FROM {local_onlineeduru_course} c JOIN {local_onlineeduru_passport} p on p.courseid = c.courseid and p.active = 1";

        $wheresql = ' WHERE c.courseid= :id';

        return $DB->get_record_sql($fields . $sql . $wheresql, ['id' => $courseid]);
    }

    public static function createPassport(int $courseid, passport $model): int
    {
        global $DB, $USER;

        $transaction = $DB->start_delegated_transaction();
        $timenow = time();

        $coursedb = new \stdClass();
        $coursedb->id = null;
        $coursedb->courseid = $courseid;
        $coursedb->usercreated = $USER->id;
        $coursedb->usermodified = $USER->id;
        $coursedb->timecreated = $timenow;
        $coursedb->timemodified = $timenow;

        $id = $DB->insert_record('local_onlineeduru_course', $coursedb);
        $coursedb->id = $id;

        $passportdb = new \stdClass();
        $passportdb->id = null;
        $passportdb->courseid = $courseid;
        $passportdb->type = helper::ACTION_CREATE;
        $passportdb->active = 1;
        $passportdb->onlineeduru_courseid = $coursedb->id;
        $passportdb->usercreated = $USER->id;
        $passportdb->usermodified = $USER->id;
        $passportdb->timecreated = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->request = $model->__toString();

        $id = $DB->insert_record('local_onlineeduru_passport', $passportdb);
        $passportdb->id = $id;

        $transaction->allow_commit();

        return $passportdb->id;
    }

    public static function updatePassport(int $courseid, passport $model): int
    {
        global $DB, $USER;

        $transaction = $DB->start_delegated_transaction();
        $timenow = time();

        $coursedb = $DB->get_record('local_onlineeduru_course', ['courseid' => $courseid], 'id');
        $coursedb->timemodified = $timenow;
        $coursedb->usermodified = $USER->id;
        $DB->update_record('local_onlineeduru_course', $coursedb);

        $passportdb = $DB->get_record('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1]);
        $passportdb->request = $model->__toString();
        $DB->update_record('local_onlineeduru_passport', $passportdb);

        $transaction->allow_commit();

        return $passportdb->id;
    }

    public static function newVersionPassport(int $courseid, passport $model): int
    {
        global $DB, $USER;

        $transaction = $DB->start_delegated_transaction();
        $timenow = time();

        $coursedb = $DB->get_record('local_onlineeduru_course', ['courseid' => $courseid], 'id');
        $coursedb->timemodified = $timenow;
        $coursedb->usermodified = $USER->id;
        $DB->update_record('local_onlineeduru_course', $coursedb);

        foreach ($DB->get_records('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], '', 'id') as $passport) {
            $passport->active = 0;
            $DB->update_record('local_onlineeduru_passport', $passport);
        }

        $passportdb = new \stdClass();
        $passportdb->id = null;
        $passportdb->courseid = $courseid;
        $passportdb->type = helper::ACTION_UPDATE;
        $passportdb->active = 1;
        $passportdb->onlineeduru_courseid = $coursedb->id;
        $passportdb->usercreated = $USER->id;
        $passportdb->usermodified = $USER->id;
        $passportdb->timecreated = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->request = $model->__toString();

        $id = $DB->insert_record('local_onlineeduru_passport', $passportdb);
        $passportdb->id = $id;

        $transaction->allow_commit();

        return $passportdb->id;
    }

    public static function getPassport(int $courseid): ?\stdClass
    {
        global $DB;

        return $DB->get_record('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], '*', MUST_EXIST);
    }

    public static function getPassportForRequest(int $courseid): string
    {
        global $DB, $USER;
        $transaction = $DB->start_delegated_transaction();
        $timenow = time();

        $passportdb = $DB->get_record('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], 'id, type, request', MUST_EXIST);
        $passportdb->timerequest = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->usermodified = $USER->id;
        $DB->update_record('local_onlineeduru_passport', $passportdb);

        $transaction->allow_commit();

        return $passportdb->request;
    }

    public static function saveResponse(int $courseid, $status, $response)
    {
        global $DB, $USER;
        $transaction = $DB->start_delegated_transaction();
        $timenow = time();

        try {
            $data = json_decode($response, true);
        } catch (\Throwable $e) {
            debugging("Ошибка при разборе response: {$e->getMessage()}");
            $data = [];
        }

        $coursedb = $DB->get_record('local_onlineeduru_course', ['courseid' => $courseid], 'id', MUST_EXIST);
        $passportdb = $DB->get_record('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], 'id, type', MUST_EXIST);

        $coursedb->timemodified = $timenow;
        $coursedb->usermodified = $USER->id;
        if ($passportdb->type == helper::ACTION_CREATE) {
            $coursedb->gis_courseid = $data['course_id'] ??  null;
        }
        $DB->update_record('local_onlineeduru_course', $coursedb);

        $passportdb->response = $response;
        $passportdb->statusresponse = $status;
        $passportdb->timeresponse = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->usermodified = $USER->id;
        $DB->update_record('local_onlineeduru_passport', $passportdb);

        $transaction->allow_commit();
    }

    public static function createParticipation(user_enrolment_created $event, ?string $gis_courseid, ?string $gis_userid): void
    {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $_participation = new \stdClass();
        $_participation->courseid = $event->courseid;
        $_participation->gis_courseid = $gis_courseid;
        $_participation->userid = $event->userid;
        $_participation->gis_userid = $gis_userid;
        $_participation->sessionid = uuid::generate();
        $_participation->timecreated = time();

        $DB->insert_record('local_onlineeduru_user', $_participation);

        $transaction->allow_commit();
    }

    public static function deleteParticipation($participation): void
    {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $participation->timedeleted = time();
        $DB->update_record('local_onlineeduru_user', $participation);

        $transaction->allow_commit();
    }

    public static function getProgress($courseid, $userid): int
    {
        global $DB;

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        return (int) round(progress::get_course_progress_percentage($course, $userid) ?? 0);
    }
}