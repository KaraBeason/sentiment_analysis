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
redirect($url, 'You will recieve a notification when your sentiment analysis reports have completed.');