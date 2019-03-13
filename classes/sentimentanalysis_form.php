<?php
namespace block_sentimentanalysis;
use block_sentimentanalysis\lib;
use moodleform;
require_once("{$CFG->libdir}/formslib.php");

class sentimentanalysis_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', get_string('availassigns', 'block_sentimentanalysis'));
        // Display assignments that have online text submissions.
        $assignments = lib::get_available_assignments();
        $mform->addElement('select', 'assignment', get_string('chosenassign', 'block_sentimentanalysis'), $assignments);
        $mform->setType('assignment', PARAM_RAW);
        // hidden elements
        $mform->addElement('hidden', 'blockid');
        $mform->addElement('hidden', 'courseid');
        $mform->setType('blockid', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);
        $this->add_action_buttons();
    }
}