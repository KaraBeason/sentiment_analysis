<?php
require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
include(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');
use block_sentimentanalysis\task\block_sentimentanalysis_task;
defined('MOODLE_INTERNAL') || die();

$blockid = required_param('blockid', PARAM_INT);

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
print_object($task);