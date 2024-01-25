<?php

namespace local_onlineeduru\form;

global $CFG;

require_once($CFG->libdir . '/formslib.php');

abstract class repeat_elements_moodleform extends \moodleform
{
    protected function addRepeatElements(string $element, array $children, array $childrenOptions = [], callable $function = null, array $data = []): void
    {
        $mform = $this->_form;

        $nameArray = "$element-array";
        $nameTitle = "$element-title";
        $nameCount = "$element-count";
        $nameAdd = "$element-add";
        $nameDelete= "$element-delete";

        $cntElements = optional_param($nameCount, 1, PARAM_INT);

        $mform->addElement('header', $nameArray, get_string("form_header_$nameArray", 'local_onlineeduru'));

        $mform->registerNoSubmitButton($nameDelete);
        // The fields to create the grade letter/boundary.
        $elements = [];
        $elements[] = $mform->createElement('static', $nameTitle, get_string("form_field_$nameTitle", 'local_onlineeduru', '{no}'));

        $elementOptions = [];

        foreach ($children as $key => $value) {
            $nameKey = "$element-$key";
            $elements[] = $mform->createElement($value, $nameKey, get_string("form_field_$nameKey", 'local_onlineeduru'), ['style' => 'width:100%']);
        }

        $elements[] = $mform->createElement('submit', $nameDelete, get_string("form_field_$nameDelete", 'local_onlineeduru'), [], false);


        // Create our repeatable elements, each one a group comprised of the fields defined previously.
        $this->repeat_elements(
            $elements,
            $cntElements,
            $elementOptions,
            $nameCount,
            $nameAdd,
            1,
            get_string("form_field_$nameAdd", 'local_onlineeduru'),
            true,
            $nameDelete
        );

        $cntDeleted = 0;

        for ($i = 0; $i <= $cntElements; $i++) {
            if ($mform->elementExists("$nameDelete-hidden[$i]")) {
                $cntDeleted++;
            }
        }

        for ($i = 0; $i <= $cntElements; $i++) {
            if ($mform->elementExists("{$nameDelete}[$i]")) {
                $mform->disabledIf("{$nameDelete}[$i]", $nameCount, 'eq', 1 + $cntDeleted);
            }

            foreach ($childrenOptions as $elementname => $elementoptions) {
                $nameKey = "$element-$elementname";

                $realelementname = "{$nameKey}[$i]";

                if (!$mform->elementExists($realelementname)) {
                    continue;
                }

                foreach ($elementoptions as  $option => $params){
                    switch ($option){
                        case 'helpbutton' :
                            $params = array_merge(array($realelementname), ["form_field_$nameKey", 'local_onlineeduru']);
                            call_user_func_array(array(&$mform, 'addHelpButton'), $params);
                            break;
                        case 'rule' :
                            foreach ($params as $param) {
                                $_params = array_merge(array($realelementname), $param);
                                call_user_func_array(array(&$mform, 'addRule'), $_params);
                            }
                            break;
                        case 'type':
                            $mform->setType($realelementname, $params);
                            break;

                        case 'expanded':
                            $mform->setExpanded($realelementname, $params);
                            break;

                        case 'advanced' :
                            $mform->setAdvanced($realelementname, $params);
                            break;
                    }
                }
            }
        }

        if (is_callable($function)) {
            $function($mform, $cntElements);
        }
    }
}