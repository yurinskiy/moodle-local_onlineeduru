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

use local_onlineeduru\services\db;

defined('MOODLE_INTERNAL') || die();

class course_passport_updated_observer
{
    public static function store(course_passport_updated $event): void
    {
        $course = $event->courseid;

        $request = db::getPassportForRequest($course);

        echo "<pre>". print_r($request, 1) . "</pre>";

        $api = new \local_onlineeduru\services\api();
        $response = $api->updateCourse($request);

        echo "<pre>". print_r($response, 1) . "</pre>";

        db::saveResponse($course, $api->getStatus(), $response);
    }
}