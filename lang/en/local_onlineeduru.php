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

$string['pluginname'] = 'Интеграция с ГИС СЦОС';
$string['onlineeduru:view'] = 'Просмотр курсов';
$string['onlineeduru:manage'] = 'Управление курсами';

$string['form_header_passport_system'] = 'Технические данные';
$string['form_field_institution'] = 'Идентификатор Правообладателя';
$string['form_field_external_url'] = 'Ссылка на онлайн-курс на сайте Платформы';
$string['form_field_business_version'] = 'Версия курса';
$string['form_header_passport'] = 'Паспорт курса';
$string['form_field_title'] = 'Название онлайн-курса';
$string['form_field_description'] = 'Описание онлайн-курса';
$string['form_field_image'] = 'Ссылка на изображение';
$string['form_field_competences'] = 'Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n';
$string['form_field_requirements'] = 'Массив строк – входных требований к обучающемуся';
$string['form_field_content'] = 'Содержание онлайн-курса';
$string['form_field_direction'] = 'Массив идентификаторов направлений в формате: “01.01.06”';
$string['form_field_lectures_number'] = 'Количество лекций';
$string['form_field_cert'] = 'Возможность получить сертификат';
$string['form_field_results'] = 'Результаты обучения';
$string['form_field_credits'] = 'Трудоёмкость курса в з.е.';

$string['validation_course_not_exists'] = 'Курс не найден';

$string['createnewcourse'] = 'Добавить курс';
$string['next'] = 'Далее';
$string['gis_courseid'] = 'Идентификатор курса в ГИС СЦОС';

$string['settings_api_endpoint'] = 'URL';
$string['settings_api_endpoint_desc'] = 'URL-адрес точки подключения к API ГИС СЦОС';
$string['settings_api_key'] = 'Уникальный ключ доступа платформы';
$string['settings_api_key_desc'] = 'Уникальный ключ доступа (X-CN-UUID) платформы к ГИС СЦОС';
$string['settings_partner_id'] = 'Идентификатор платформы';
$string['settings_partner_id_desc'] = 'Идентификатор платформы (partner_id)';
$string['settings_institution'] = 'Идентификатор правообладателя';
$string['settings_institution_desc'] = 'Идентификатор правообладателя (institution)';
$string['settings_client_id'] = 'Идентификатор платформы';
$string['settings_client_id_desc'] = 'Идентификатор платформы (client_id) в системе единой аутентификации';
$string['settings_client_secret'] = 'Секретный ключ доступа платформы';
$string['settings_client_secret_desc'] = 'Секретный ключ доступа платформы (client_secret) к системе единой аутентификации';
