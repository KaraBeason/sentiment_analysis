<?php

require_once(__DIR__ . '/lib.php');

class block_sentimentanalysis extends block_base {
    public function init() {
        $this->title = get_string('sentimentanalysis', 'block_sentimentanalysis');
    }

    public function get_content() {
        global $COURSE, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);

        $this->content         =  new stdClass;
        $executetask = new moodle_url('/blocks/sentimentanalysis/execute_task.php', array('blockid' => $this->instance->id));
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