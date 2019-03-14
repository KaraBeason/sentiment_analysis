<?php
namespace block_sentimentanalysis\task;
class block_sentimentanalysis_task extends \core\task\adhoc_task {
    /**
     *
     */
    public function execute()
    {
        global $DB;
        // Custom data returned as decoded json as defined in classes\task\adhoc_task.
        $assignment = $this->get_custom_data();
        $assignment = $assignment->assignment;
        $text_submissions = $DB->get_records_sql("SELECT *
                                        FROM mdl_assignsubmission_onlinetext t
                                        WHERE t.assignment = '$assignment'");
        // Make temp directory and write all assignment submissions to it.
        $dir = make_temp_directory('sentiment_analysis');
        foreach ($text_submissions as $sub)
        {
            // TODO: need unique way to identify whose assignment it is.
            $myfile = fopen($dir . "\submission_" . $sub->id . ".txt", "w");
            fwrite($myfile, strip_tags($sub->onlinetext));
            fclose($myfile);
        }

        exec('C:\Python27\python '. __DIR__ . '\\sentiments_analysis.py C:\\xampp\\moodledata\/temp\/sentiment_analysis', $output, $return);
        if (!$return) {
            echo "PDF Created Successfully";
        } else {
            echo "PDF not created";
        }
    }
}
