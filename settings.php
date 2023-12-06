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

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('root', new admin_category('local_onlineeduru', new lang_string('pluginname', 'local_onlineeduru')), 'users');

    if (has_capability('local/onlineeduru:manager', context_system::instance())) {

        $settings = new admin_settingpage(
            'local_onlineeduru_settings',
            get_string('settings'));

        if ($ADMIN->fulltree) {
            $settings->add(new admin_setting_heading(
                'local_onlineeduru/test_connection',
                'Тестирование',
                format_text(sprintf('После сохранения настроек протестируйте подключение к API ГИС СЦОС. Для этого перейдите по [ссылке](%s).', \local_onlineeduru\helper::get_test_connection_url()), FORMAT_MARKDOWN)));


            $settings->add(new admin_setting_heading('local_onlineeduru/settings', 'Настройки поключения к ГИС СЦОС', ''));

            $settings->add(new admin_setting_configselect(
                'local_onlineeduru/api_endpoint',
                get_string('settings_api_endpoint', 'local_onlineeduru'),
                get_string('settings_api_endpoint_help', 'local_onlineeduru'),
                'https://test.online.edu.ru/api/v2/',
                [
                    'https://online.edu.ru/api/v2/' => 'API V2 (бой)',
                    'https://test.online.edu.ru/api/v2/' => 'API V2 (тест)',
                ]
            ));

            $settings->add(new admin_setting_configtext(
                'local_onlineeduru/api_key',
                get_string('settings_api_key', 'local_onlineeduru'),
                get_string('settings_api_key_help', 'local_onlineeduru'),
                '',
                PARAM_ALPHANUMEXT
            ));

            $settings->add(new admin_setting_configtext(
                'local_onlineeduru/partner_id',
                get_string('settings_partner_id', 'local_onlineeduru'),
                get_string('settings_partner_id_help', 'local_onlineeduru'),
                '',
                PARAM_ALPHANUMEXT
            ));

            $settings->add(new admin_setting_configtext(
                'local_onlineeduru/institution',
                get_string('settings_institution', 'local_onlineeduru'),
                get_string('settings_institution_help', 'local_onlineeduru'),
                '',
                PARAM_ALPHANUMEXT
            ));

            $settings->add(new admin_setting_configtext(
                'local_onlineeduru/client_id',
                get_string('settings_client_id', 'local_onlineeduru'),
                get_string('settings_client_id_help', 'local_onlineeduru'),
                '',
                PARAM_TEXT
            ));

            $settings->add(new admin_setting_configtext(
                'local_onlineeduru/client_secret',
                get_string('settings_client_secret', 'local_onlineeduru'),
                get_string('settings_client_secret_help', 'local_onlineeduru'),
                '',
                PARAM_ALPHANUM
            ));
        }

        /** @var admin_category $ADMIN */
        $ADMIN->add('local_onlineeduru', $settings);
    }
}