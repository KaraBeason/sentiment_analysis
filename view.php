<?php

require_once('../../config.php');
require_once('sentimentanalysis_form.php');
include(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');
use block_sentimentanalysis\task\block_sentimentanalysis_task;

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
//    print_object($fromform);
    $assignment = $fromform->assignment;
    $text_submissions = $DB->get_records_sql("SELECT *
                                        FROM mdl_assignsubmission_onlinetext t
                                        WHERE t.assignment = '$assignment'");
    $context = context_module::instance($courseid);

    $dir = make_temp_directory('sentiment_analysis');
    foreach ($text_submissions as $sub)
    {
        // TODO: need unique way to identify whose assignment it is.
        $myfile = fopen($dir . "\submission_" . $sub->id . ".txt", "w");
        fwrite($myfile, strip_tags($sub->onlinetext));
        fclose($myfile);
    }
    // create the ad hoc task.
    $sentiment_analyzer = new block_sentimentanalysis_task();
    // set blocking if required (it probably isn't)
    //     $sentiment_analyzer->set_blocking(true);
    // add custom data

    $sentiment_analyzer->set_custom_data(array(
        'directory_name' => $dir,
    ));
        \core\task\manager::queue_adhoc_task($sentiment_analyzer);
        print_object($sentiment_analyzer);
    // queue it
} else
{
    echo $OUTPUT->header();
    $sentimentanalysis->display();
    echo $OUTPUT->footer();
}

// TODO: this should be used when creating the report file FROM the results of the python script.
// File creation as seen in moodle File API.
//    $fs = get_file_storage();
//        print_object($sub);
//    // Prepare file record object
//        $fileinfo = array(
//            'contextid' => $context->id, // ID of context
//            'component' => 'block_sentimentanalysis',     // usually = table name
//            'filearea' => 'temp',     // usually = table name
//            'itemid' => 0,               // usually = ID of row in table
//            'filepath' => '/'. $dir . '/',           // any path beginning and ending in /
//            'filename' => 'submission_' . $sub->id . '.txt'); // any filename
//
//    // Create file containing text 'hello world'
//        $fs->create_file_from_string($fileinfo, "hi there");
