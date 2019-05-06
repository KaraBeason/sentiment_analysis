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
require_once(__DIR__ . '/lib.php');

class block_sentimentanalysis extends block_base {
    public function init() {
        $this->title = get_string('sentimentanalysis', 'block_sentimentanalysis');
    }

    public function get_content() {
        global $COURSE;

        $context = context_course::instance($COURSE->id);
        // Check current user's capabilities.
        if (!has_capability('moodle/course:update', $context))
        {
            return;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
        $executetask = new moodle_url('/blocks/sentimentanalysis/execute_task.php', 
            array('blockid' => $this->instance->id));
        $this->content->text = '<a href="'.$executetask.'">'.get_string('executetask', 'block_sentimentanalysis').'</a>';

        return $this->content;
    }

    //method that returns an associative array of attribute names and values, allowing us to change defaut behavior
    //  of block display
    public function html_attributes()
    {
        $attributes = parent::html_attributes(); // get default values
        $attributes['class'] .= ' block_' . $this->name(); // append our class to class attribute
        return $attributes;
    }

    // this block can only be added to the site front page or any course view.
    public function applicable_formats() {
        return array(
            'course-view' => true);
    }
}