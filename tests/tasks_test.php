<?php
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
            
        $results = $DB->get_recordset_sql($sql);
        $this->assertNotNull($results);
    }
}