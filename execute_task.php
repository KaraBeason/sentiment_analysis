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
require_once(__DIR__ . '/classes/task/block_sentimentanalysis_task.php');

use block_sentimentanalysis\task\block_sentimentanalysis_task;

defined('MOODLE_INTERNAL') || die();

// We pass/fetch the block instance id
$id = required_param('id', PARAM_INT);

// Get the instance record using the block id
$instancerec = $DB->get_record('block_instances', array('id' => $id));
if (!$instancerec) {
    // No instance, no service
    throw new \moodle_exception(get_string('invalidblockinstance', 'error', 'sentimentanalysis'));
}

$block = block_instance("sentimentanalysis", $instancerec);
// Block instance rec leads to parent (course) context
$coursecontext = context::instance_by_id($block->instance->parentcontextid);

// Now I got the course id in the instanceid member. If anytihing goes wrong
// want to go back to the course view.
$PAGE->set_url('/course/view.php', array('id' => $coursecontext->instanceid));

// Know which course we're in, so can authorize the user
require_login($coursecontext->instanceid);
// Check current user's capabilities.
require_capability('moodle/course:update', $coursecontext);

// At this user is authenticated, and authorized to submit the task
$blockinstance = block_instance('sentimentanalysis', $instancerec);

// Config is deserialized from text column, assignments in
// array, create the ad hoc task and supply the needed ids
$task = new block_sentimentanalysis_task();
$task->set_custom_data(array(
    'assignmentids' => $blockinstance->config->assignments,
    'user' => $USER->id
    ));

// Queue it, then back to the course.
\core\task\manager::queue_adhoc_task($task);
//Adding message to redirect until/unless I can figure out how to display it in the block upon clicking the execute button.
redirect($PAGE->url, "You will recieve a notification when your sentiment analysis reports have completed.");
