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
 * block_sentimentanalysis
 *
 * @author      Kara Beason <beasonke@appstate.edu>
 * @copyright   (c) 2019 Appalachian State Universtiy, Boone, NC
 * @license     GNU General Public License version 3
 * @package     block_sentimentanalysis
 */
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/lib.php');

class block_sentimentanalysis_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
 
        global $COURSE;

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $assignments = get_available_assignments($COURSE->id);
        $options = array();
        foreach ($assignments as $id => $assignname) {
            // // Fix up some markup for the form
            $options[$id] =  $assignname->name;
        }        
        $select = $mform->addElement('select', 'config_assignments', get_string('assignlist', 'block_sentimentanalysis'), $options);
        $select->setMultiple(true);
        $mform->setType('config_assignments', PARAM_RAW);

    }
}