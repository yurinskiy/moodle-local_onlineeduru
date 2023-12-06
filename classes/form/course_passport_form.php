<?php

namespace local_onlineeduru\form;

global $CFG;

use core_course_list_element;
use MoodleQuickForm;

require_once($CFG->libdir . '/formslib.php');

class course_passport_form extends \moodleform
{
    protected function definition()
    {
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $course = $customdata['course'];
        $repeatno = $customdata['teachercount'];

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
        $mform->setDefault('institution', get_config('local_onlineeduru', 'institution'));
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
        $mform->addHelpButton('title', 'form_field_title', 'local_onlineeduru');
        $mform->addRule('title', get_string('required'), 'required');
        $mform->setDefault('title', $course->fullname);

        // Дата ближайшего запуска
        $mform->addElement('date_selector', 'started_at', get_string('form_field_started_at', 'local_onlineeduru'));
        $mform->addHelpButton('started_at', 'form_field_started_at', 'local_onlineeduru');
        $mform->addRule('started_at', get_string('required'), 'required');
        // Дата окончания онлайн-курса
        $mform->addElement('date_selector', 'finished_at', get_string('form_field_finished_at', 'local_onlineeduru'));
        $mform->addHelpButton('finished_at', 'form_field_finished_at', 'local_onlineeduru');
        // Дата окончания записи на онлайн-курс
        $mform->addElement('date_selector', 'enrollment_finished_at', get_string('form_field_enrollment_finished_at', 'local_onlineeduru'));
        $mform->addHelpButton('enrollment_finished_at', 'form_field_enrollment_finished_at', 'local_onlineeduru');

        // Описание онлайн-курса
        $mform->addElement('textarea', 'description', get_string('form_field_description', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('description', 'form_field_description', 'local_onlineeduru');
        $mform->addRule('description', get_string('required'), 'required');
        $mform->setDefault('description', strip_tags($course->summary));

        // Ссылка на изображение
        $mform->addElement('text', 'image', get_string('form_field_image', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('image', PARAM_URL);
        $mform->addHelpButton('image', 'form_field_image', 'local_onlineeduru');
        $mform->addRule('image', get_string('required'), 'required');
        $mform->setDefault('image', local_onlineeduru_get_course_image_url($course));

        // Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n
        $mform->addElement('textarea', 'competences', get_string('form_field_competences', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('competences', 'form_field_competences', 'local_onlineeduru');
        // Массив строк – входных требований к обучающемуся
        $mform->addElement('textarea', 'requirements', get_string('form_field_requirements', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('requirements', 'form_field_requirements', 'local_onlineeduru');
        // Содержание онлайн-курса
        $mform->addElement('textarea', 'content', get_string('form_field_content', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('requirements', 'form_field_requirements', 'local_onlineeduru');
        // Массив идентификаторов направлений в формате: “01.01.06”
        $mform->addElement('text', 'direction', get_string('form_field_direction', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('direction', PARAM_TEXT);
        $mform->addHelpButton('direction', 'form_field_direction', 'local_onlineeduru');
        // Количество лекций
        $mform->addElement('float', 'lectures_number', get_string('form_field_lectures_number', 'local_onlineeduru'), ['size' => '255']);
        $mform->addHelpButton('lectures_number', 'form_field_lectures_number', 'local_onlineeduru');
        // Возможность получить сертификат
        $mform->addElement('select', 'cert', get_string('form_field_cert', 'local_onlineeduru'), [
            false => 'Нет',
            true => 'Да',
        ]);
        $mform->addHelpButton('cert', 'form_field_cert', 'local_onlineeduru');

        // Результаты обучения
        $mform->addElement('textarea', 'results', get_string('form_field_results', 'local_onlineeduru'), ['style' => 'width:100%']);
        // Трудоёмкость курса в з.е.
        $mform->addElement('float', 'credits', get_string('form_field_credits', 'local_onlineeduru'), ['size' => '255']);


        $mform->addElement('header', 'course_teachers', get_string('form_header_course_teachers', 'local_onlineeduru'));

        $mform->registerNoSubmitButton('teacher_delete');
        // The fields to create the grade letter/boundary.
        $elements = [];
        $elements[] = $mform->createElement('static', 'teacher_name', 'Лектор {no}');
        $elements[] = $mform->createElement('text', 'teacher_display_name', get_string('form_field_teacher_display_name', 'local_onlineeduru'));
        $elements[] = $mform->createElement('text', 'teacher_image', get_string('form_field_teacher_image', 'local_onlineeduru'));
        $elements[] = $mform->createElement('text', 'teacher_description', get_string('form_field_teacher_description', 'local_onlineeduru'));
        $elements[] = $mform->createElement('submit', 'teacher_delete', get_string('form_field_teacher_delete', 'local_onlineeduru'), [], false);


        // Element options/rules, fields should be disabled unless "Override" is checked for course grade letters.
        $options = [];
        $options['teacher_display_name']['type'] = PARAM_TEXT;
        $options['teacher_image']['type'] = PARAM_URL;
        $options['teacher_description']['type'] = PARAM_TEXT;

        // Create our repeatable elements, each one a group comprised of the fields defined previously.
        $this->repeat_elements(
            $elements,
            $repeatno,
            $options,
            'teachercount',
            'teachertryadd',
            1,
            'Добавить лектора',
            true,
            'teacher_delete'
        );

        for ($i = 0; $i <= $repeatno; $i++) {
            if ($mform->elementExists("teacher_display_name[$i]")) {
                $mform->addRule("teacher_display_name[$i]", get_string('required'), 'required');
            }
            if ($mform->elementExists("teacher_image[$i]")) {
                $mform->addRule("teacher_image[$i]", get_string('required'), 'required');
            }
        }

        $this->add_action_buttons();
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if (array_key_exists('title', $data) && mb_strlen($data['title']) > 255) {
            $errors['title'] = get_string('validation_course_not_exists', 'local_onlineeduru');
        }

        return $errors;
    }
}