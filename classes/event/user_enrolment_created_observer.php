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

namespace local_onlineeduru\event;

use core\event\user_enrolment_created;
use core_user;
use local_onlineeduru\services\api;
use local_onlineeduru\services\db;

defined('MOODLE_INTERNAL') || die();

class user_enrolment_created_observer
{
    public static function store(user_enrolment_created $event): void
    {
        global $DB;

        $course = $DB->get_record('local_onlineeduru_course', ['courseid' => $event->courseid]);

        if (!$course) {
            return;
        }

        $user = core_user::get_user($event->userid);

        if (!$user) {
            return;
        }

        $uuid = (new api())->getUserID($user->email);

        if (!$uuid) {
            return;
        }

        db::createParticipation($event, $course->gis_courseid, $uuid);
    }
}