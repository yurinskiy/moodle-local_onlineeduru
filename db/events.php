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

$observers = [
    [
        'eventname' => '\local_onlineeduru\event\course_passport_created',
        'callback'  => '\local_onlineeduru\event\course_passport_created_observer::store',
        'priority'  => 1000,
    ],
    [
        'eventname' => '\local_onlineeduru\event\course_passport_updated',
        'callback'  => '\local_onlineeduru\event\course_passport_updated_observer::store',
        'priority'  => 1000,
    ],
    [
        'eventname' => '\core\event\user_enrolment_created',
        'callback'  => '\local_onlineeduru\event\user_enrolment_created_observer::store',
        'priority'  => 9999,
    ],
    [
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => '\local_onlineeduru\event\user_enrolment_deleted_observer::store',
        'priority'  => 9999,
    ],
    [
        'eventname' => '\core\event\user_graded',
        'callback'  => '\local_onlineeduru\event\user_graded_observer::store',
        'priority'  => 9999,
    ],
];
