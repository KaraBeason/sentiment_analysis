<?php


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
            $this->content->text   = "ad hoc task to analyze sentiment in assignments<br>" . $this->get_current_assignments();
            $this->content->text .= "<br>context: " . $this->page->context->id;
        }
        else {
            $this->content->text = "not logged in...";
        }

        global $COURSE;

        $url = new moodle_url('/blocks/sentimentanalysis/view.php'
            , array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
        $this->content->footer = html_writer::link($url, get_string('chooseassignlink', 'block_sentimentanalysis'));
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

    public function get_current_assignments()
    {
        global $DB;
        $result = $DB->get_records_sql('SELECT assignment
                                        FROM mdl_assignsubmission_onlinetext t
                                        INNER JOIN mdl_assign asn ON t.assignment = asn.id');
        $assignments = "";
        foreach ($result as $row)
        {
            $assignments .= "assignment: " . $row->assignment ."<br>";
        }

        return $assignments;
    }

}