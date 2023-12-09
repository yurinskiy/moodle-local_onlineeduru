<?php

namespace local_onlineeduru\services;

use local_onlineeduru\event\course_passport_created;
use local_onlineeduru\helper;
use local_onlineeduru\model\passport;

class db
{
    public static function list(array $params = [], int $page = 0, int $perpage = 20): array
    {
        global $DB;

        $fields = "SELECT c.*, p.*, p.id as passportid";
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
        $coursedb->timecreated = $timenow;
        $coursedb->timemodified = $timenow;
        $coursedb->usercreated = $USER->id;
        $coursedb->usermodified = $USER->id;

        $coursedb->id = $DB->insert_record('local_onlineeduru_course', $coursedb);

        $passportdb = new \stdClass();
        $passportdb->id = null;
        $passportdb->onlineeduru_courseid = $coursedb->id;
        $passportdb->courseid = $courseid;
        $passportdb->type = helper::ACTION_CREATE;
        $passportdb->active = 1;
        $passportdb->request = $model->__toString();
        $passportdb->timecreated = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->usercreated = $USER->id;
        $passportdb->usermodified = $USER->id;

        $passportdb->id = $DB->insert_record('local_onlineeduru_passport', $passportdb);

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

        foreach ($DB->get_records('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], '', 'id') as $passport) {
            $passport->active = 0;
            $DB->update_record('local_onlineeduru_passport', $passport);
        }

        $passportdb = new \stdClass();
        $passportdb->id = null;
        $passportdb->onlineeduru_courseid = $coursedb->id;
        $passportdb->courseid = $courseid;
        $passportdb->type = helper::ACTION_UPDATE;
        $passportdb->active = 1;
        $passportdb->request = $model;
        $passportdb->timecreated = $timenow;
        $passportdb->timemodified = $timenow;
        $passportdb->usercreated = $USER->id;
        $passportdb->usermodified = $USER->id;
        $DB->insert_record('local_onlineeduru_passport', $passportdb);

        $transaction->allow_commit();

        return $passportdb->id;
    }
}