<?php

require_once(__DIR__ . '/lib.php');
include(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');

use block_sentimentanalysis\task\block_sentimentanalysis_task;


class block_sentimentanalysis extends block_base {
    public function init() {
        $this->title = get_string('sentimentanalysis', 'block_sentimentanalysis');
    }

    public function get_content() {
        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);

        $this->content         =  new stdClass;
        if(has_capability('block/sentimentanalysis:viewpages', $context))
        {
            $this->content->text   = "Task to analyze sentiment in assignments<br>";
        }
       
        $url = new moodle_url('/blocks/sentimentanalysis/selection.php'
            , array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
        $this->content->footer = html_writer::tag('button', get_string('executetask', 'block_sentimentanalysis'),
            array("onclick"=>$this->execute_adhoc_task()));
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

    // Execute sentiment analysis task on all assignments configured from block instance config.
    public function execute_adhoc_task()
    {
        global $USER;
        // create the ad hoc task.
        $task = new block_sentimentanalysis_task();
        // Pass ad hoc task the id of the assignment and the current user.
        $task->set_custom_data(array(
            'assignment' => $this->config->assignments,
            'user' => $USER->id
            ));
        // Queue it.
        \core\task\manager::queue_adhoc_task($task);
        print_object($task);
    }
}