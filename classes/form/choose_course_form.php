<?php

namespace local_onlineeduru\form;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class choose_course_form extends \moodleform
{
    protected function definition() {
        $mform = $this->_form;

        $strrequired = get_string('required');

        $mform->_attributes['style'] = 'height:400px';

        $options = ['multiple' => false, 'includefrontpage' => false];
        $mform->addElement('course', 'courseid', get_string('courses'), $options);
        $mform->addRule('courseid', $strrequired, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('next', 'local_onlineeduru'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $strrequired = get_string('required');

        if (!array_key_exists('courseid', $errors) && empty($data['courseid'])) {
            $errors['courseid'] = $strrequired;
        }
        if (!array_key_exists('courseid', $errors) && !$this->get_course($data['courseid'])) {
            $errors['courseid'] = get_string('validation_course_not_exists', 'local_onlineeduru');
        }

        return $errors;
    }

    public function get_course($courseid) {
        global $DB;

        return $DB->get_record('course', array('id' => $courseid));
    }
}