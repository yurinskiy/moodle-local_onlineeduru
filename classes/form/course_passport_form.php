<?php

namespace local_onlineeduru\form;

use core_course_list_element;

class course_passport_form extends repeat_elements_moodleform
{
    protected function definition()
    {
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $course = $customdata['course'] ?? null;
        $passport = $customdata['passport'] ?? null;

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
        $mform->setDefault('title', $passport['title'] ?? $course->fullname);

        // Дата ближайшего запуска
        $mform->addElement('date_selector', 'started_at', get_string('form_field_started_at', 'local_onlineeduru'));
        $mform->addHelpButton('started_at', 'form_field_started_at', 'local_onlineeduru');
        $this->setRequired('started_at');
        $mform->setDefault('started_at', !empty($passport['started_at'])  ? \DateTime::createFromFormat('Y-m-d', $passport['started_at'])->getTimestamp() : null);

        // Дата окончания онлайн-курса
        $mform->addElement('date_selector', 'finished_at', get_string('form_field_finished_at', 'local_onlineeduru'), array('optional' => true));
        $mform->addHelpButton('finished_at', 'form_field_finished_at', 'local_onlineeduru');
        $mform->setDefault('finished_at', !empty($passport['finished_at']) ? \DateTime::createFromFormat('Y-m-d', $passport['finished_at'])->getTimestamp() : null);

        // Дата окончания записи на онлайн-курс
        $mform->addElement('date_selector', 'enrollment_finished_at', get_string('form_field_enrollment_finished_at', 'local_onlineeduru'), array('optional' => true));
        $mform->addHelpButton('enrollment_finished_at', 'form_field_enrollment_finished_at', 'local_onlineeduru');
        $mform->setDefault('enrollment_finished_at', !empty($passport['enrollment_finished_at']) ? \DateTime::createFromFormat('Y-m-d', $passport['enrollment_finished_at'])->getTimestamp() : null);

        // Описание онлайн-курса
        $mform->addElement('textarea', 'description', get_string('form_field_description', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('description', 'form_field_description', 'local_onlineeduru');
        $mform->addRule('description', get_string('required'), 'required');
        $mform->setDefault('description', $passport['description'] ?? strip_tags($course->summary));

        // Ссылка на изображение
        $mform->addElement('text', 'image', get_string('form_field_image', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('image', PARAM_URL);
        $mform->addHelpButton('image', 'form_field_image', 'local_onlineeduru');
        $this->setRequired('image');
        $mform->setDefault('image', $passport['image'] ?? local_onlineeduru_get_course_image_url($course));

        // Содержание онлайн-курса
        $mform->addElement('textarea', 'content', get_string('form_field_content', 'local_onlineeduru'), ['style' => 'width:100%']);
        $mform->addHelpButton('content', 'form_field_content', 'local_onlineeduru');
        $mform->setDefault('content', $passport['content'] ?? null);

        // Возможность получить сертификат
        $mform->addElement('select', 'cert', get_string('form_field_cert', 'local_onlineeduru'), [
            false => 'Нет',
            true => 'Да',
        ]);
        $this->setRequired('cert');
        $mform->addHelpButton('cert', 'form_field_cert', 'local_onlineeduru');
        $mform->setDefault('cert', $passport['cert'] ?? false);

        // Результаты обучения
        $mform->addElement('textarea', 'results', get_string('form_field_results', 'local_onlineeduru'), ['style' => 'width:100%']);
        $this->setRequired('results');
        $mform->setDefault('results', $passport['results'] ?? null);

        // Строка с набором компетенций. Для разделения строк по позициям необходимо использовать \n
        $passport['competences'] = \explode('\n', $passport['competences'] ?? '');
        $_POST['competence-count'] = max(\count($passport['competences'] ?? []) , 1);
        $this->addRepeatElements('competence', [
            'value' => 'text'
        ], [
            'value' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required']
                ],
            ]
        ], function ($_mform, $cntElements) use ($passport) {
            for ($i = 0; $i <= $cntElements; $i++) {
                if ($_mform->elementExists("competence-value[$i]")) {
                    $_mform->setDefault("competence-value[$i]", $passport['competences'][$i] ?? null);
                }
            }
        });
        // Массив строк – входных требований к обучающемуся
        $_POST['requirement-count'] = max(\count($passport['requirements'] ?? []) , 1);
        $this->addRepeatElements('requirement', [
            'value' => 'text'
        ], [
            'value' => [
                'type' => PARAM_TEXT,
                'rule' => [
                    [get_string('required'), 'required']
                ],
            ]
        ], function ($_mform, $cntElements) use ($passport) {
            for ($i = 0; $i <= $cntElements; $i++) {
                if ($_mform->elementExists("requirement-value[$i]")) {
                    $_mform->setDefault("requirement-value[$i]", $passport['requirements'][$i] ?? null);
                }
            }
        });
        // Массив идентификаторов направлений в формате: “01.01.06”
        $_POST['direction-count'] = max(\count($passport['direction'] ?? []) , 1);
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
        ], function ($_mform, $cntElements) use ($passport) {
            for ($i = 0; $i <= $cntElements; $i++) {
                if ($_mform->elementExists("direction-value[$i]")) {
                    $_mform->setDefault("direction-value[$i]", $passport['direction'][$i] ?? null);
                }
            }
        });

        // Лекторы
        $_POST['teacher-count'] = max(\count($passport['teachers'] ?? []) , 1);
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
        ], function ($_mform, $cntElements) use ($passport) {
            for ($i = 0; $i <= $cntElements; $i++) {
                if ($_mform->elementExists("teacher-display_name[$i]")) {
                    $_mform->setDefault("teacher-display_name[$i]", $passport['teachers'][$i]['display_name'] ?? null);
                }
                if ($_mform->elementExists("teacher-image[$i]")) {
                    $_mform->setDefault("teacher-image[$i]", $passport['teachers'][$i]['image'] ?? null);
                }
                if ($_mform->elementExists("teacher-description[$i]")) {
                    $_mform->setDefault("teacher-description[$i]", $passport['teachers'][$i]['description'] ?? null);
                }
            }
        });

        $this->addTimes($passport);

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

    protected function addTimes($passport = null)
    {
        $mform = $this->_form;

        $mform->addElement('header', 'times', 'Продолжительность онлайн-курса');

        // Длительность онлайн-курса в неделях
        $mform->addElement('text', 'duration_value', get_string('form_field_duration_value', 'local_onlineeduru'));
        $mform->addHelpButton('duration_value', 'form_field_duration', 'local_onlineeduru');
        $mform->setType('duration_value', PARAM_TEXT);
        $this->setRequired('duration_value');
        $mform->setDefault('duration_value', $passport['duration']['value'] ?? null);

        $mform->addElement('select', 'duration_code', get_string('form_field_duration_code', 'local_onlineeduru'), ['week' => 'недель']);
        $this->setRequired('duration_code');
        $mform->setDefault('duration_code', $passport['duration']['code'] ?? null);

        $mform->addElement('html', '<hr>');

        // Трудоёмкость курса в з.е.
        $mform->addElement('text', 'lectures', get_string('form_field_lectures_number', 'local_onlineeduru'), ['size' => '255']);
        $mform->addHelpButton('lectures', 'form_field_lectures_number', 'local_onlineeduru');
        $mform->setType('lectures', PARAM_TEXT);
        $this->setRequired('lectures');
        $mform->setDefault('lectures', $passport['lectures'] ?? null);


        $mform->addElement('html', '<hr>');

        // Трудоёмкость курса в з.е.
        $mform->addElement('text', 'credits', get_string('form_field_credits', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('credits', PARAM_TEXT);
        $this->setRequired('credits');
        $mform->setDefault('credits', $passport['credits'] ?? null);

        // Объем онлайн-курса в часах
        $mform->addElement('text', 'hours', get_string('form_field_hours', 'local_onlineeduru'), ['size' => '255']);
        $mform->setType('hours', PARAM_TEXT);
        $mform->setDefault('hours', $passport['hours'] ?? null);
    }
}