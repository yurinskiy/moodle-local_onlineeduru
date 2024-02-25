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
$string['course_passport_created'] = 'Создание паспорта для интеграции с ГИС СЦОС';
$string['course_passport_updated'] = 'Обновление паспорта для интеграции с ГИС СЦОС';

$string['form_header_passport_system'] = 'Технические данные';
$string['form_field_institution'] = 'Идентификатор Правообладателя';
$string['form_field_external_url'] = 'Ссылка на онлайн-курс на сайте Платформы';
$string['form_field_business_version'] = 'Версия курса';
$string['form_header_passport'] = 'Паспорт онлайн-курса';

$string['form_field_title'] = 'Наименование онлайн-курса';
$string['form_field_title_help'] = 'Например: Ядерная физика';
$string['form_field_started_at'] = 'Дата ближайшего запуска';
$string['form_field_started_at_help'] = 'Например: 2022-09-30';
$string['form_field_finished_at'] = 'Дата окончания онлайн-курса';
$string['form_field_finished_at_help'] = 'Например: 2022-12-31';
$string['form_field_enrollment_finished_at'] = 'Дата окончания записи на онлайн-курс';
$string['form_field_enrollment_finished_at_help'] = 'Например: 2022-11-10';
$string['form_field_description'] = 'Описание онлайн-курса';
$string['form_field_description_help'] = 'Например: Ядерная физика является одним из основных разделов физики, связанных с описанием свойств материи.';
$string['form_field_image'] = 'Ссылка на изображение баннера онлайн-курса';
$string['form_field_image_help'] = 'Например: https://online.edu.ru/static/courses/i/01.01/course/11.jpg';
$string['form_field_content'] = 'Содержание онлайн-курса';
$string['form_field_content_help'] = 'Например: <ul><li>Модуль 1. Общие свойства ядер</li></ul>';
$string['form_field_lectures_number'] = 'Количество лекций';
$string['form_field_lectures_number_help'] = 'Например: 12';
$string['form_field_cert'] = 'Возможность получить сертификат';
$string['form_field_cert_help'] = 'Например: Да';
$string['form_field_results'] = 'Результаты обучения';
$string['form_field_results_help'] = 'Результаты обучения';
$string['form_field_credits'] = 'Трудоёмкость онлайн-курса в з.е.';
$string['form_field_credits_help'] = 'Например: 5';
$string['form_field_hours'] = 'Объем онлайн-курса в часах';
$string['form_field_hours_help'] = 'Например: 72';
$string['form_field_duration'] = 'Длительность онлайн-курса';
$string['form_field_duration_code'] = 'Код вида длительности';
$string['form_field_duration_value'] = 'Длительность онлайн-курса';
$string['form_field_duration_value_help'] = 'Например: 5 недель';

$string['form_header_course_teachers'] = 'Лекторы курса';
$string['form_field_teacher_display_name'] = 'ФИО лектора';
$string['form_field_teacher_display_name_help'] = 'Например: Иванов Иван Иванович';
$string['form_field_teacher_image'] = 'Ссылка на изображение лектора';
$string['form_field_teacher_image_help'] = 'Например: https://online.edu.ru/static/courses/i/01.01/rating/2.jpg';
$string['form_field_teacher_description'] = 'Описание лектора';
$string['form_field_teacher_description_help'] = 'Например: Кандидат педагогических наук, доцент.';
$string['form_field_teacher_delete'] = 'Удалить лектора';

$string['form_header_competence-array'] = 'Компетенции';
$string['form_field_competence-title'] = 'Компетенция №{$a}';
$string['form_field_competence-value'] = 'Описание компетенции';
$string['form_field_competence-value_help'] = 'Например: Компетенция 1';
$string['form_field_competence-delete'] = 'Удалить компетенцию';
$string['form_field_competence-add'] = 'Добавить компетенцию';

$string['form_header_requirement-array'] = 'Требования к обучающемуся';
$string['form_field_requirement-title'] = 'Требование №{$a}';
$string['form_field_requirement-value'] = 'Описание требования';
$string['form_field_requirement-value_help'] = 'Например: Знание русского языка';
$string['form_field_requirement-delete'] = 'Удалить требование';
$string['form_field_requirement-add'] = 'Добавить требование';

$string['form_header_direction-array'] = 'Направления обучения';
$string['form_field_direction-title'] = 'Направление №{$a}';
$string['form_field_direction-value'] = 'Направление в формате "01.01.01"';
$string['form_field_direction-value_help'] = 'Например: 01.01.01';
$string['form_field_direction-delete'] = 'Удалить направление';
$string['form_field_direction-add'] = 'Добавить направление';

$string['form_header_teacher-array'] = 'Лекторы';
$string['form_field_teacher-title'] = 'Лектор №{$a}';
$string['form_field_teacher-display_name'] = 'ФИО лектора';
$string['form_field_teacher-display_name_help'] = 'Например: Иванов Иван Иванович';
$string['form_field_teacher-image'] = 'Ссылка на изображение лектора';
$string['form_field_teacher-image_help'] = 'Например: https://online.edu.ru/static/courses/i/01.01/rating/2.jpg';
$string['form_field_teacher-description'] = 'Описание лектора';
$string['form_field_teacher-description_help'] = 'Например: Кандидат педагогических наук, доцент.';
$string['form_field_teacher-delete'] = 'Удалить лектора';
$string['form_field_teacher-add'] = 'Добавить лектора';


$string['validation_course_not_exists'] = 'Курс не найден';

$string['createnewcourse'] = 'Добавить онлайн-курс';
$string['next'] = 'Далее';
$string['gis_courseid'] = 'Идентификатор онлайн-курса в ГИС СЦОС';

$string['logs'] = 'История запросов';
$string['log_by_id'] = 'Просмотр запроса №{$a}';

$string['settings_api_endpoint'] = 'URL';
$string['settings_api_endpoint_help'] = 'URL-адрес точки подключения к API ГИС СЦОС';
$string['settings_api_key'] = 'Уникальный ключ доступа платформы';
$string['settings_api_key_help'] = 'Уникальный ключ доступа (X-CN-UUID) платформы к ГИС СЦОС';
$string['settings_partner_id'] = 'Идентификатор платформы';
$string['settings_partner_id_help'] = 'Идентификатор платформы (partner_id)';
$string['settings_institution'] = 'Идентификатор правообладателя';
$string['settings_institution_help'] = 'Идентификатор правообладателя (institution)';
$string['settings_client_id'] = 'Идентификатор платформы';
$string['settings_client_id_help'] = 'Идентификатор платформы (client_id) в системе единой аутентификации';
$string['settings_client_secret'] = 'Секретный ключ доступа платформы';
$string['settings_client_secret_help'] = 'Секретный ключ доступа платформы (client_secret) к системе единой аутентификации';
