<?php
namespace block_sentimentanalysis\task;
class block_sentimentanalysis_task extends \core\task\adhoc_task {
    /**
     *
     */
    public function execute()
    {
        // Custom data returned as decoded json as defined in classes\task\adhoc_task.
        $text_submissions = $this->get_custom_data();
        $text_submissions = $text_submissions->submissions;
        var_dump($text_submissions);
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
        // Return will return non-zero upon an error
        if (!$return) {
            echo "PDF Created Successfully";
        } else {
            echo "PDF not created";
        }
    }
}
