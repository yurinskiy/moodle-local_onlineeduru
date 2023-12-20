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

use core\event\user_enrolment_deleted;
use local_onlineeduru\services\db;

defined('MOODLE_INTERNAL') || die();

class user_enrolment_deleted_observer
{
    public static function store(user_enrolment_deleted $event): void
    {
        global $DB;

        $participation = $DB->get_record('local_onlineeduru_user', ['courseid' => $event->courseid, 'userid' => $event->userid, 'timedeleted' => null]);

        echo '<pre>'.print_r($participation, true);

        if (!$participation) {
            return;
        }

        db::deleteParticipation($participation);
    }
}