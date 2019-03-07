<?php
namespace block_sentimentanalysis\task;
class block_sentimentanalysis_task extends \core\task\adhoc_task {
    /**
     *
     */
    public function execute()
    {
        exec('C:\Python27\python '. __DIR__ . '\\sentiments_analysis.py C:\\xampp\\moodledata\/temp\/sentiment_analysis', $output, $return);
        // Return will return non-zero upon an error
        if (!$return) {
            echo "PDF Created Successfully";
        } else {
            echo "PDF not created";
        }
    }
}
