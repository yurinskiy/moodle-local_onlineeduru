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
    const VIEW_PATH = '/local/onlineeduru/view.php';
    const EDIT_PATH = '/local/onlineeduru/edit.php';
    const RESEND_PATH = '/local/onlineeduru/resend.php';
    const TEST_CONNECTION_PATH = '/local/onlineeduru/test.php';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_NEW_VERSION = 'version';
    public const ACTION_RESEND_PASSPORT = 'resendPassport';

    public static function get_create_passport_url(int $courseid = null) : moodle_url {
        $params = ['action' => self::ACTION_CREATE];
        if (!empty($courseid) ){
            $params['id'] = $courseid;
        }
        return new moodle_url(self::EDIT_PATH, $params);
    }

    public static function get_update_passport_url(int $courseid) : moodle_url {
        return new moodle_url(self::EDIT_PATH, ['id' => $courseid, 'action' => self::ACTION_UPDATE]);
    }

    public static function get_update_new_passport_url(int $courseid) : moodle_url {
        return new moodle_url(self::EDIT_PATH, ['id' => $courseid, 'action' => self::ACTION_NEW_VERSION]);
    }

    public static function get_resend_url(int $passportid) : moodle_url {
        return new moodle_url(self::RESEND_PATH, ['id' => $passportid]);
    }

    public static function get_view_passport_url(int $courseid) : moodle_url {
        return new moodle_url(self::VIEW_PATH, ['id' => $courseid]);
    }

    public static function get_passports() : moodle_url {
        return new moodle_url(self::MANAGER_PATH);
    }

    public static function get_version_passport($courseid): int {
        global $DB;

        $request = $DB->get_record('local_onlineeduru_passport', ['courseid' => $courseid, 'active' => 1], 'request');

        if (!$request) {
            return 1;
        }

        return  json_decode($request->request, true, 512, JSON_THROW_ON_ERROR)['business_version'] + 1;
    }

    public static function get_test_connection_url() : moodle_url {
        return new moodle_url(self::TEST_CONNECTION_PATH);
    }

}