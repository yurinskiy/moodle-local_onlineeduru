<?php

namespace local_onlineeduru\event;

use core\event\user_graded;

class user_graded_observer
{
    public static function store(user_graded $event): void
    {
        global $DB, $USER;

        $participation = $DB->get_record('local_onlineeduru_user', ['courseid' => $event->courseid, 'userid' => $event->userid, 'timedeleted' => null]);

        if (!$participation) {
            return;
        }

        $grade = $DB->get_record('local_onlineeduru_grade', [
            'courseid' => $event->courseid,
            'userid' => $event->userid,
            'sessionid' => $participation->sessionid,
            'checkpoint_id' => sprintf('COURSE#%s#USER#%s#ITEM#%s', $event->courseid, $event->userid, $event->get_grade()->itemid)
        ]);

        $now = time();

        if (!$grade) {
            $grade = new \stdClass();
            $grade->id = null;
            $grade->courseid = $event->courseid;
            $grade->userid = $event->userid;
            $grade->gis_courseid = $participation->gis_courseid;
            $grade->gis_userid = $participation->gis_userid;
            $grade->sessionid = $participation->sessionid;
            $grade->checkpoint_id = sprintf('COURSE#%s#USER#%s#ITEM#%s', $event->courseid, $event->userid, $event->get_grade()->itemid);
            $grade->timecreated = $now;
        }

        $grade->checkpoint_name = $event->get_grade()->grade_item->get_name();
        $grade->usermodified = $USER->id;
        $grade->timemodified = $now;

        $grade->rating = grade_format_gradevalue($event->get_grade()->finalgrade, $event->get_grade()->grade_item, false, GRADE_DISPLAY_TYPE_PERCENTAGE);

        $grade->rating = trim(str_replace('%', '', $grade->rating));

        if (null === $grade->id) {
            $DB->insert_record('local_onlineeduru_grade', $grade);
        } else {
            $grade->uuid_request = null;
            $DB->update_record('local_onlineeduru_grade', $grade);
        }
    }
}