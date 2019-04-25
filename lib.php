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
 * @copyright   (c) 2017 Appalachian State Universtiy, Boone, NC
 * @license     GNU General Public License version 3
 * @package     block_sentimentanalysis
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Returns a list of all assignments that have associated online text submissions.
 */
function get_available_assignments($courseid, array $assignids = null)
{
    global $DB;

    $sql = 'SELECT DISTINCT asn.id, name '
         . ' FROM mdl_assignsubmission_onlinetext t '
         . ' INNER JOIN mdl_assign asn ON t.assignment = asn.id '
         . ' WHERE course = ' . $courseid;

    // Submit the query
    $result = $DB->get_records_sql($sql);

    return $result;
}
