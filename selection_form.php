<?php
require_once("{$CFG->libdir}/formslib.php");
require_once (__DIR__ . '/lib.php');

defined('MOODLE_INTERNAL') || die();

class block_sentimentanalysis_selection_form extends moodleform {

    function definition() {
        global $COURSE;

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('availassigns', 'block_sentimentanalysis'));
        // Display assignments that have online text submissions.
        $assignments = get_available_assignments($COURSE->id);
        $options = array();
        foreach ($assignments as $id => $assignname) {
            // // Fix up some markup for the form
            $options[$id] =  $assignname->name;
        }
        $mform->addElement('select', 'assignment', get_string('chosenassign', 'block_sentimentanalysis'), $options);
        $mform->setType('assignment', PARAM_RAW);
        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
        $mform->setType('blockid', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);
        $this->add_action_buttons();
    }
}