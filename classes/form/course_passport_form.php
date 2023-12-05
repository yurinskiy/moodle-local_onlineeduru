<?php

namespace local_onlineeduru\form;

global $CFG;

use core_course_list_element;

require_once($CFG->libdir . '/formslib.php');

class course_passport_form extends \moodleform
{
    protected function definition()
    {
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $course = $customdata['course'];

        if ($course instanceof \stdClass) {
            $course = new core_course_list_element($course);
        }

        $version = $customdata['version'];

        $mform->addElement('header', 'passport_system', get_string('form_header_passport_system', 'local_onlineeduru'));

        $mform->addElement('hidden', 'courseid', $course->id);
        $mform->setType('courseid', PARAM_INT);
        // Идентификатор Правообладателя
        $mform->addElement('text', 'institution', get_string('form_field_institution', 'local_onlineeduru'), ['size' => '36', 'disabled' => true]);
        $mform->setType('institution', PARAM_ALPHANUMEXT);
        $mform->setDefault('institution', 'из настроек');
        // Ссылка на онлайн-курс на сайте Платформы
        $mform->addElement('text', 'external_url', get_string('form_field_external_url', 'local_onlineeduru'), ['disabled' => true]);
        $mform->setType('external_url', PARAM_URL);
        $mform->setDefault('external_url', course_get_url($course));
        // Версия курса
        $mform->addElement('text', 'business_version', get_string('form_field_business_version', 'local_onlineeduru'), ['size' => '36', 'disabled' => true]);
        $mform->setType('business_version', PARAM_ALPHANUMEXT);
        $mform->setDefault('business_version', $version);

        $mform->addElement('header', 'passport', get_string('form_header_passport', 'local_onlineeduru'));

        // Название онлайн-курса
        $mform->addElement('text', 'title', get_string('form_field_title', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('error'), 'required');
        $mform->setDefault('title', $course->fullname);
        // Описание онлайн-курса
        $mform->addElement('textarea', 'description', get_string('form_field_description', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->setDefault('description', strip_tags($course->summary));
        // Ссылка на изображение
        $mform->addElement('text', 'image', get_string('form_field_image', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('image', PARAM_URL);
        $mform->setDefault('image', local_onlineeduru_get_course_image_url($course));
        // Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n
        $mform->addElement('textarea', 'competences', get_string('form_field_competences', 'local_onlineeduru'), ['style' => 'width:100%']);
        // Массив строк – входных требований к обучающемуся
        $mform->addElement('textarea', 'requirements', get_string('form_field_requirements', 'local_onlineeduru'), ['style' => 'width:100%']);
        // Содержание онлайн-курса
        $mform->addElement('textarea', 'content', get_string('form_field_content', 'local_onlineeduru'), ['style' => 'width:100%']);
        // Массив идентификаторов направлений в формате: “01.01.06”
        $mform->addElement('text', 'direction', get_string('form_field_direction', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('direction', PARAM_TEXT);
        // Количество лекций
        $mform->addElement('float', 'lectures_number', get_string('form_field_lectures_number', 'local_onlineeduru'), ['size' => '255']);
        // Возможность получить сертификат
        $mform->addElement('select', 'cert', get_string('form_field_cert', 'local_onlineeduru'), [
            false => 'Нет',
            true => 'Да',
        ]);
        // Массив лекторов
        // TODO

        // Результаты обучения
        $mform->addElement('textarea', 'results', get_string('form_field_results', 'local_onlineeduru'), ['style' => 'width:100%']);
        // Трудоёмкость курса в з.е.
        $mform->addElement('float', 'credits', get_string('form_field_credits', 'local_onlineeduru'), ['size' => '255']);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (array_key_exists('title', $data)  && mb_strlen($data['title']) > 255) {
            $errors['title'] = get_string('validation_course_not_exists', 'local_onlineeduru');
        }

        return $errors;
    }
}