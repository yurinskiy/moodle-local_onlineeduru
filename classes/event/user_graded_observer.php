<?php

namespace local_onlineeduru\event;

use core\event\user_graded;

class user_graded_observer
{
    public static function store(user_graded $event): void
    {
        global $DB, $USER, $CFG;

        require_once($CFG->libdir . '/gradelib.php');
        require_once("$CFG->dirroot/grade/querylib.php");


        $courseid = $event->courseid;
        $userid = $event->relateduserid;

        $participation = $DB->get_record('local_onlineeduru_user', ['courseid' => $courseid, 'userid' => $userid, 'timedeleted' => null]);

        if (!$participation) {
            return;
        }

        if ($event->userid === -1) {
            self::fillProgress($courseid, $userid, $participation);

            return;
        }

        $grade = $DB->get_record('local_onlineeduru_grade', [
            'courseid' => $courseid,
            'userid' => $userid,
            'sessionid' => $participation->sessionid,
            'checkpoint_id' => sprintf('COURSE#%s#USER#%s#ITEM#%s', $courseid, $userid, $event->get_grade()->itemid)
        ]);

        $now = time();

        if (!$grade) {
            $grade = new \stdClass();
            $grade->id = null;
            $grade->courseid = $courseid;
            $grade->userid = $userid;
            $grade->gis_courseid = $participation->gis_courseid;
            $grade->gis_userid = $participation->gis_userid;
            $grade->sessionid = $participation->sessionid;
            $grade->checkpoint_id = sprintf('COURSE#%s#USER#%s#ITEM#%s', $courseid, $userid, $event->get_grade()->itemid);
            $grade->timecreated = $now;
        }

        $grade->checkpoint_name = $event->get_grade()->grade_item->get_name();
        $grade->usermodified = $USER->id;
        $grade->timemodified = $now;

        if ($event->get_grade()->finalgrade !== null) {
            $grade->rating = grade_format_gradevalue($event->get_grade()->finalgrade, $event->get_grade()->grade_item, false, GRADE_DISPLAY_TYPE_PERCENTAGE);

            $grade->rating = trim(str_replace('%', '', $grade->rating));
        } else {
            $grade->rating = 0;
        }

        $grade->progress = 0; //round(grade_get_course_grade($userid, $courseid)->grade ?? 0, 2);

        if (null === $grade->id) {
            $DB->insert_record('local_onlineeduru_grade', $grade);
        } else {
            $grade->uuid_request = null;
            $DB->update_record('local_onlineeduru_grade', $grade);
        }
    }

    protected static function fillProgress($courseid, $userid, $participation)
    {
        global $USER, $DB;

        $progress = $DB->get_record('local_onlineeduru_progress', [
            'courseid' => $courseid,
            'userid' => $userid,
            'sessionid' => $participation->sessionid,
            'uuid_request' => null,
        ]);

        $now = time();

        if (!$progress) {
            $progress = new \stdClass();
            $progress->courseid = $courseid;
            $progress->userid = $userid;
            $progress->gis_courseid = $participation->gis_courseid;
            $progress->gis_userid = $participation->gis_userid;
            $progress->sessionid = $participation->sessionid;
            $progress->timecreated = $now;
        }

        $progress->usermodified = $USER->id;
        $progress->timemodified = $now;
        $progress->progress = round(grade_get_course_grade($userid, $courseid)->grade ?? 0, 2);

        if (null === $progress->id) {
            $DB->insert_record('local_onlineeduru_progress', $progress);
        } else {
            $progress->uuid_request = null;
            $DB->update_record('local_onlineeduru_progress', $progress);
        }
    }
}