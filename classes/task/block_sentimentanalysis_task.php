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

namespace block_sentimentanalysis\task;
use \context_user;

defined('MOODLE_INTERNAL') || die();

/**
 *  Ad hoc task executed a python script to analyze online text submissions for an assignment
 *  for sentiment.  The resulting report is saved into the user's private file area.
 */
class block_sentimentanalysis_task extends \core\task\adhoc_task {

    public function execute()
    {
        global $DB;
        $path_to_temp_folder = 'C:\\xampp\\moodledata\\temp\\sentiment_analysis\\'; // Path to where the work is done during the task.
        // Custom data returned as decoded json as defined in classes\task\adhoc_task.
        $custom_data = $this->get_custom_data();
        $userid = $custom_data->user;
        $assignmentids = $custom_data->assignmentids;
        // Datetime to differentiate between iterations of the task reports.
        $datetime = new \DateTime('NOW');
        foreach ($assignmentids as $assignment)
        {
            $sql = "SELECT usr.firstname, usr.lastname, t.onlinetext
                FROM mdl_assignsubmission_onlinetext t
                INNER JOIN mdl_assign_submission sub on sub.id = t.submission
                INNER JOIN mdl_user usr on usr.id = sub.userid
                WHERE t.assignment = '$assignment' and sub.status = 'submitted'";
            $text_submissions = $DB->get_recordset_sql($sql);
            // if result contains zero records, move on to the next assignment.
            if ($text_submissions->valid() == false)
            {
                continue;
            }
            $sql = "SELECT asn.name
                    FROM mdl_assign asn
                    WHERE asn.id = $assignment";
            $record = $DB->get_record_sql($sql);
            $assign_name = $record->name;

            // Make temp directory and write all assignment submissions to it.
            $dir = make_temp_directory('sentiment_analysis');
            foreach ($text_submissions as $record => $row)
            {
                $name = $row->firstname . " " . $row->lastname;
                $myfile = fopen($dir . "\\" . $name . "_" . $assign_name . ".txt", "w");
                fwrite($myfile, strip_tags($row->onlinetext));
                fclose($myfile);
            }
            // Execute python script to process the text submissions.
            exec('C:\Python27\python '. __DIR__ . '\\sentiments_analysis.py ' . $path_to_temp_folder, $output, $return);
            if (!$return) {
                mtrace("... Sentiment analylsis completed.");
            } else {
                mtrace("... Unknown failure during sentiment analysis.");
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
                    $record->itemid, $record->filepath, $assign_name . ' ' . $datetime->format('Y-m-d H:i:s') . '.pdf');
            // Ensure file is readable/exists.
            if (!is_readable($path_to_temp_folder . $filename))
            {
                mtrace("... File '. $path_to_temp_folder . $filename . ' does not exist or is not readable.");
                return;
            }
            if ($fs->create_file_from_pathname($record, $path_to_temp_folder . $filename))
            {
                mtrace("... File uploaded successfully as {$record->filename}.");
            } else {
                mtrace("... Unknown failure during creation.");
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

        // Email user to let them know their reports are completed and uploaded in their private file section.
        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = 2; // Admin
        $message->userto = $userid;
        $message->subject = 'Sentiment Analysis Complete';
        $message->fullmessage = 'Please check the "Sentiment Analysis" folder in your private file area to view reports.';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>Please check the "Sentiment Analysis" folder in your private file area to view reports.</p>';
        $message->smallmessage = 'Please check the "Sentiment Analysis" folder in your private file area to view reports.';

        $message->courseid = 4; // This is required in recent versions, use it from 3.2 on https://tracker.moodle.org/browse/MDL-47162

        $messageid = message_send($message);

        }
    }