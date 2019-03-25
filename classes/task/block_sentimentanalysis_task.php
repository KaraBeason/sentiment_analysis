<?php
namespace block_sentimentanalysis\task;
use \context_user;
class block_sentimentanalysis_task extends \core\task\adhoc_task {
    /**
     *
     */
    public function execute()
    {
        global $DB, $USER;

        // Custom data returned as decoded json as defined in classes\task\adhoc_task.
        $assignment = $this->get_custom_data();
        $assignment = $assignment->assignment;
        $text_submissions = $DB->get_records_sql("SELECT usr.username, t.onlinetext
                                        FROM mdl_assignsubmission_onlinetext t
                                        INNER JOIN mdl_assign_submission sub on sub.assignment = t.assignment
                                        INNER JOIN mdl_user usr on usr.id = sub.userid
                                        WHERE t.assignment = '$assignment' and sub.status = 'submitted'");
                                        
        // Make temp directory and write all assignment submissions to it.
        $dir = make_temp_directory('sentiment_analysis');
        foreach ($text_submissions as $username => $onlinetext)
        {
            $myfile = fopen($dir . "\\" . $username . "_" . $assignment . ".txt", "w");
            fwrite($myfile, strip_tags($onlinetext->onlinetext));
            fclose($myfile);
        }

        exec('C:\Python27\python '. __DIR__ . '\\sentiments_analysis.py C:\\xampp\\moodledata\/temp\/sentiment_analysis', $output, $return);
        if (!$return) {
            echo "PDF Created Successfully";
        } else {
            echo "PDF not created";
        }
        // Save the file into teacher's private file area.
        $context = context_user::instance($USER->id);
        // File creation as seen in moodle File API.
        $fs = get_file_storage();
        //    // Prepare file record object
        $record = new \stdClass();
        $record->filearea   = 'private';
        $record->component  = 'user';
        $record->filepath   = '\/sentimentanalysis\/';
        $record->itemid     = 0;
        $record->contextid  = $context->id;
        $record->userid     = $USER->id;

        $record->filename = $fs->get_unused_filename($context->id, $record->component, $record->filearea,
                $record->itemid, $record->filepath, "sentiment_test_file.txt");
    
        if ($fs->create_file_from_string($record, "hi I'm a test file")) {
            // File created successfully.
            mtrace("---- File uploaded successfully as {$record->filename}.");
        } else {
            mtrace("---- Unknown failure during creation.");
        }

    }
}