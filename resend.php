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

use core\uuid;
use local_onlineeduru\helper;
use local_onlineeduru\services\db;

global $CFG, $DB, $OUTPUT, $PAGE, $SITE;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/filelib.php');


$id = required_param('id', PARAM_INT);
$action = optional_param('action', helper::ACTION_RESEND_PASSPORT, PARAM_ALPHA);

$systemcontext = $context = context_system::instance();

/** Проверяем авторизован ли пользователь */
require_login();

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/onlineeduru:manage', $context)) {
    header('Location: ' . $CFG->wwwroot);
    die();
}

switch ($action) {
    case helper::ACTION_RESEND_PASSPORT:
        $passportdb = db::getPassport($id);
        $request = db::getPassportForRequest($id);

        switch ($passportdb->type ?? null) {
            case helper::ACTION_CREATE:
                $api = new \local_onlineeduru\services\api();
                $response = $api->createCourse(uuid::generate(), $request);

                db::saveResponse($id, $api->getStatus(), $response);
                break;
            case helper::ACTION_UPDATE:

                $api = new \local_onlineeduru\services\api();
                $response = $api->updateCourse(uuid::generate(), $request);

                echo "<pre>" . print_r($response, 1) . "</pre>";

                db::saveResponse($id, $api->getStatus(), $response);
                break;
            default:
                throw new \coding_exception('Неизвестная операция над паспортом!');
        }

        if ($api->getStatus() != '200') {
            redirect(helper::MANAGER_PATH, 'Ошибка: ' . $response, null, \core\output\notification::NOTIFY_ERROR);
        }

        redirect(helper::MANAGER_PATH, 'Данные отправлены успешно');
        break;
    default:
        redirect(helper::MANAGER_PATH, 'Действия по отправке не найдено', null, \core\output\notification::NOTIFY_ERROR);
        break;
}