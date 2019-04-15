<?php
require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
include(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');
use block_sentimentanalysis\task\block_sentimentanalysis_task;
defined('MOODLE_INTERNAL') || die();

global $PAGE;


$blockid = required_param('blockid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$PAGE->set_url('/blocks/sentimentanalysis/execute_task.php', 
    array('blockid' => $blockid, 'courseid' => $courseid));
$instance = $DB->get_record('block_instances', array('id' => $blockid));
$blockname = 'sentimentanalysis';
$block = block_instance($blockname, $instance);
$assignments = $block->config->assignments;
// create the ad hoc task.
$task = new block_sentimentanalysis_task();
// Pass ad hoc task the id of the assignment and the current user.
$task->set_custom_data(array(
    'assignment' => $assignments,
    'user' => $USER->id
    ));
// Queue it.
\core\task\manager::queue_adhoc_task($task);

// Redirect to main course page.
$url= new moodle_url('/course/view.php', array('id' => $courseid));
// echo "You will recieve an e-mail when your sentiment analysis reports have completed.";
redirect($url, 'You will recieve an e-mail when your sentiment analysis reports have completed.');