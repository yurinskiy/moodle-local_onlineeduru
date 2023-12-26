<?php

namespace local_onlineeduru\event;

use core\event\course_module_completion_updated;
use local_onlineeduru\services\db;

class course_module_completion_updated_observer
{
    public static function store($event): void
    {
        global $DB, $USER;

        $participation = $DB->get_record('local_onlineeduru_user', ['courseid' => $event->courseid, 'userid' => $event->userid, 'timedeleted' => null]);

        if (!$participation) {
            return;
        }

        $progress = $DB->get_record('local_onlineeduru_progress', [
            'courseid' => $event->courseid,
            'userid' => $event->userid,
            'sessionid' => $participation->sessionid,
            'uuid_request' => null,
        ]);

        $now = time();

        if (!$progress) {
            $progress = new \stdClass();
            $progress->courseid = $event->courseid;
            $progress->userid = $event->userid;
            $progress->gis_courseid = $participation->gis_courseid;
            $progress->gis_userid = $participation->gis_userid;
            $progress->sessionid = $participation->sessionid;
            $progress->timecreated = $now;
        }

        $progress->usermodified = $USER->id;
        $progress->timemodified = $now;
        $progress->progress = db::getProgress($progress->courseid, $progress->userid);

        if (null === $progress->id) {
            $DB->insert_record('local_onlineeduru_progress', $progress);
        } else {
            $progress->uuid_request = null;
            $DB->update_record('local_onlineeduru_progress', $progress);
        }
    }
}