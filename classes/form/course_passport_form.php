<?php

namespace local_onlineeduru\form;

use core_course_list_element;

class course_passport_form extends repeat_elements_moodleform
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
        $this->setRequired('title');
        $mform->addRule('title', 'Количество введённых символов должно быть от 1 до 255', 'rangelength', [1, 255]);
        $mform->setDefault('title', $course->fullname);

        // Дата ближайшего запуска
        $mform->addElement('date_selector', 'started_at', get_string('form_field_started_at', 'local_onlineeduru'));
        $mform->addHelpButton('started_at', 'form_field_started_at', 'local_onlineeduru');
        $this->setRequired('started_at');
        // Дата окончания онлайн-курса
        $mform->addElement('date_selector', 'finished_at', get_string('form_field_finished_at', 'local_onlineeduru'), array('optional' => true));
        $mform->addHelpButton('finished_at', 'form_field_finished_at', 'local_onlineeduru');
        // Дата окончания записи на онлайн-курс
        $mform->addElement('date_selector', 'enrollment_finished_at', get_string('form_field_enrollment_finished_at', 'local_onlineeduru'), array('optional' => true));
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
        $this->setRequired('image');
        $mform->setDefault('image', local_onlineeduru_get_course_image_url($course));

        // Содержание онлайн-курса
        $mform->addElement('textarea', 'content', get_string('form_field_content', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('content', 'form_field_content', 'local_onlineeduru');
        // Возможность получить сертификат
        $mform->addElement('select', 'cert', get_string('form_field_cert', 'local_onlineeduru'), [
            false => 'Нет',
            true => 'Да',
        ]);
        $this->setRequired('cert');
        $mform->addHelpButton('cert', 'form_field_cert', 'local_onlineeduru');

        // Результаты обучения
        $mform->addElement('textarea', 'results', get_string('form_field_results', 'local_onlineeduru'), ['style' => 'width:100%']);
        $this->setRequired('results');

        // Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n
        $this->addRepeatElements('competence', [
            'value' => 'text'
        ], [
            'value' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required']
                ],
            ]
        ]);
        // Массив строк – входных требований к обучающемуся
        $this->addRepeatElements('requirement', [
            'value' => 'text'
        ], [
            'value' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required']
                ],
            ]
        ]);
        // Массив идентификаторов направлений в формате: “01.01.06”
        $this->addRepeatElements('direction', [
            'value' => 'text'
        ], [
            'value' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required'],
                    ['Не соответствует маске', 'regex', '/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/'],
                ],
            ]
        ]);

        // Лекторы
        $this->addRepeatElements('teacher', [
            'display_name' => 'text',
            'image' => 'text',
            'description' => 'text'
        ], [
            'display_name' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required'],
                    ['Количество введённых символов должно быть от 1 до 255', 'rangelength', [1, 255]],
                ],
                'helpbutton' => true,
            ],
            'image' => [
                'type' => PARAM_URL,
                'rule' => [
                    [get_string('required'), 'required']
                ],
                'helpbutton' => true,
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'helpbutton' => true,
            ]
        ]);

        $this->addTimes();

        $this->add_action_buttons();
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        return $errors;
    }

    protected function setRequired(string $element): void
    {
        $this->_form->addRule($element, get_string('required'), 'required');
    }

    protected function addTimes()
    {
        $mform = $this->_form;

        $mform->addElement('header', 'times', 'Продолжительность курса');

        // Длительность онлайн-курса в неделях
        $mform->addElement('static', 'duration', get_string('form_field_duration', 'local_onlineeduru'));
        $mform->addHelpButton('duration', 'form_field_duration', 'local_onlineeduru');
        $mform->addElement('text', 'duration_value', get_string('form_field_duration_value', 'local_onlineeduru'));
        $mform->setType('duration_value', PARAM_TEXT);
        $this->setRequired('duration_value');
        $mform->addElement('select', 'duration_code', get_string('form_field_duration_code', 'local_onlineeduru'), ['week' => 'недель']);
        $this->setRequired('duration_code');


        $mform->addElement('html', '<hr>');


        // Трудоёмкость курса в з.е.
        $mform->addElement('text', 'credits', get_string('form_field_credits', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('credits', PARAM_TEXT);
        $this->setRequired('credits');

        // Объем онлайн-курса в часах
        $mform->addElement('text', 'hours', get_string('form_field_hours', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('hours', PARAM_TEXT);
    }
}