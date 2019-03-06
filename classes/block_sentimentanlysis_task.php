<?php

class block_sentimentanalysis_task extends \core\task\adhoc_task {
    public function execute() {
        exec('C:\Python27\python sentiments_analysis.py ' . $this->directory_name);
    }
}
