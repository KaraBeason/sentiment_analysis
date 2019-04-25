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
 * block_sentimentanalysis tests
 *
 * @author      Kara Beason <beasonke@appstate.edu>
 * @copyright   (c) 2019 Appalachian State Universtiy, Boone, NC
 * @license     GNU General Public License version 3
 * @package     block_sentimentanalysis
 */
include(__DIR__ . '/../classes/task/block_sentimentanalysis_task.php');
use block_sentimentanalysis\task\block_sentimentanalysis_task;

defined('MOODLE_INTERNAL') || die();

class block_sentimentanalysis_tasks_testcase extends advanced_testcase {
    public function test_create_task()
    {
        global $DB;

        $this->resetAfterTest(true);
        $this->setUser(2);                    // switch $USER to admin
        // set up task
        $assignments = array("548", "582");
        // create the ad hoc task.
        $task = new block_sentimentanalysis_task();
        // Pass ad hoc task the id of the assignment and the current user.
        $task->set_custom_data(array(
            'assignment' => $assignments,
            'user' => 2
            ));
        // Queue it.
        \core\task\manager::queue_adhoc_task($task);

        $sql = "SELECT *
                FROM phpu_task_adhoc";
            
        $record = $DB->get_record_sql($sql);
        $this->assertNotNull($results);

        // exec('php ' . __DIR__ . '\..\..\..\admin\tool\task\cli\adhoc_task.php --execute', $output, $return);
        // var_dump($output);
        $now = time();
        // Get it from the scheduler.
        $task = \core\task\manager::adhoc_task_from_record($record);
        $this->assertInstanceOf('\\core\\task\\adhoc_test_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);
    }
}