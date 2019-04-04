<?php
namespace block_sentimentanalysis\task;
use \context_user;
defined('MOODLE_INTERNAL') || die();

class block_sentimentanalysis_task extends \core\task\adhoc_task {
    /**
     *  Ad hoc task executed a python script to analyze online text submissions for an assignment
     *  for sentiment.  The resulting report is saved into the user's private file area.
     */
    public function execute()
    {
        global $DB;
        $path_to_temp_folder = 'C:\\xampp\\moodledata\\temp\\sentiment_analysis\\'; // Path to where the work is done during the task.
        // Custom data returned as decoded json as defined in classes\task\adhoc_task.
        $custom_data = $this->get_custom_data();
        $userid = $custom_data->user;
        $assignments = $custom_data->assignment;
        // Datetime to differentiate between iterations of the task reports.
        $datetime = new \DateTime('NOW');
        foreach ($assignments as $assignment)
        {
            $text_submissions = $DB->get_recordset_sql("SELECT usr.username, t.onlinetext
                                            FROM mdl_assignsubmission_onlinetext t
                                            INNER JOIN mdl_assign_submission sub on sub.assignment = t.assignment
                                            INNER JOIN mdl_user usr on usr.id = sub.userid
                                            WHERE t.assignment = '$assignment' and sub.status = 'submitted'");
                                            
            // Make temp directory and write all assignment submissions to it.
            $dir = make_temp_directory('sentiment_analysis');
            foreach ($text_submissions as $record => $row)
            {
                $myfile = fopen($dir . "\\" . $row->username . "_" . $assignment . ".txt", "w");
                fwrite($myfile, strip_tags($row->onlinetext));
                fclose($myfile);
            }
            // Execute python script to process the text submissions.
            exec('C:\Python27\python '. __DIR__ . '\\sentiments_analysis.py ' . $path_to_temp_folder, $output, $return);
            if (!$return) {
                mtrace("---- Sentiment analylsis completed.");
            } else {
                mtrace("---- Unknown failure during sentiment analysis.");
            }

            // Create a file record and save the file produced by the python script into the teacher's private file area.
            $fs = get_file_storage();
            // Name of the file expected from the python script.
            $filename = 'output.pdf';
            $context = context_user::instance($userid);
        
            // Prepare file record object
            $record = new \stdClass();
            $record->filearea   = 'private';
            $record->component  = 'user';
            $record->filepath   = '\\sentimentanalysis\\';
            $record->itemid     = 0;
            $record->contextid  = $context->id;
            $record->userid     = $userid;

            $record->filename = $fs->get_unused_filename($context->id, $record->component, $record->filearea,
                    $record->itemid, $record->filepath, $assignment . '_' . $datetime->format('Y-m-d H:i:s') . '.pdf');
            // Ensure file is readable/exists.
            if (!is_readable($path_to_temp_folder . $filename))
            {
                mtrace("---- File '. $path_to_temp_folder . $filename . ' does not exist or is not readable.");
                return;
            }
            if ($fs->create_file_from_pathname($record, $path_to_temp_folder . $filename))
            {
                mtrace("---- File uploaded successfully as {$record->filename}.");
            } else {
                mtrace("---- Unknown failure during creation.");
            }
        }

        // Clean up temp folder.
        $files = glob($path_to_temp_folder . '\\*');
        foreach($files as $file)
        {
            if (is_file($file))
            {
                unlink($file);
            }
        }
    }
}