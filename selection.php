<?php

require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/selection_form.php');

use block_sentimentanalysis\task\block_sentimentanalysis_task;



include(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');

global $DB, $OUTPUT, $PAGE;

// Check for all required variables.
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_sentimentanalysis', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/sentimentanalysis/view.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('chooseassign', 'block_sentimentanalysis'));

$submittedform = new block_sentimentanalysis_selection_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$submittedform->set_data($toform);

if($submittedform->is_cancelled())
{
    // Cancelled form redirects to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($data = $submittedform->get_data())
{
    // create the ad hoc task.
    $task = new block_sentimentanalysis_task();
    // set blocking if required (it probably isn't)
    //     $sentiment_analyzer->set_blocking(true);
    // add custom data

    $task->set_custom_data(array(
        'assignment' => $data->assignment,
    ));
    // queue it
        \core\task\manager::queue_adhoc_task($task);
        print_object($task);
} else
{
    echo $OUTPUT->header();
    $submittedform->display();
    echo $OUTPUT->footer();
}