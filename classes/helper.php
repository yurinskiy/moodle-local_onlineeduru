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

/**
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_onlineeduru;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * @package   local_onlineeduru
 * @copyright 2023, Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper
{
    const MANAGER_PATH = '/local/onlineeduru/index.php';
    const EDIT_PATH = '/local/onlineeduru/edit.php';
    const TEST_CONNECTION_PATH = '/local/onlineeduru/test.php';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';

    public static function get_create_passport_url() : moodle_url {
        return new moodle_url(self::EDIT_PATH, ['action' => self::ACTION_CREATE]);
    }

    public static function get_update_passport_url(int $courseid, string $action = self::ACTION_CREATE) : moodle_url {
        return new moodle_url(self::EDIT_PATH, ['id' => $courseid, 'action' => $action]);
    }

    public static function get_version_passport($courseid): int {
        global $DB;

        return  $DB->count_records('local_onlineeduru_passport', ['courseid' => $courseid]) + 1;
    }

    public static function get_test_connection_url() : moodle_url {
        return new moodle_url(self::TEST_CONNECTION_PATH);
    }

}