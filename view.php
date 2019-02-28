<?php

require_once('../../config.php');
require_once('sentimentanalysis_form.php');

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

$sentimentanalysis = new sentimentanalysis_form();
$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;
$sentimentanalysis->set_data($toform);

if($sentimentanalysis->is_cancelled())
{
    // Cancelled form redirects to the course main page.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($fromform = $sentimentanalysis->get_data())
{
    // Submission is validated and ad hoc task is called.
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    // Read all online text submissions for selecte assignment into a temporary directory.
    // TODO: move this code to another file? seems messy here.
    print_object($fromform);
    $assignment = $fromform->assignment;
    $text_submissions = $DB->get_records_sql("SELECT *
                                        FROM mdl_assignsubmission_onlinetext t
                                        WHERE t.assignment = '$assignment'");
//    print_object($text_submissions);
    $context = context_module::instance($courseid);
    print_object($context);

//    create_directory($context, 'block_sentimentanalysis', $filearea, $itemid, $filepath);
} else
{
    echo $OUTPUT->header();
    $sentimentanalysis->display();
    echo $OUTPUT->footer();
}