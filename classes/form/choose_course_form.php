<?php

namespace local_onlineeduru\form;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class choose_course_form extends \moodleform
{
    protected function definition() {
        $mform = $this->_form;

        $mform->_attributes['style'] = 'height:400px';

        $mform->addElement('course', 'courseid', get_string('courses'), ['multiple' => false, 'includefrontpage' => false]);
        $mform->addRule('courseid', get_string('required'), 'required');

        $this->add_action_buttons(true, get_string('next', 'local_onlineeduru'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!array_key_exists('courseid', $errors) && $data['courseid'] && !get_course($data['courseid'])) {
            $errors['courseid'] = get_string('validation_course_not_exists', 'local_onlineeduru');
        }


        if (!array_key_exists('courseid', $errors) && $data['courseid'] && $this->get_pasport_course($data['courseid'])) {
            $errors['courseid'] = 'Данный курс уже зарегистрирован в системе';
        }

        return $errors;
    }

    public function get_course($courseid) {
        global $DB;

        return $DB->get_record('course', array('id' => $courseid));
    }

    public function get_pasport_course($courseid) {
        global $DB;

        return $DB->get_record('local_onlineeduru_course', array('courseid' => $courseid));
    }
}