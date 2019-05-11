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
defined('MOODLE_INTERNAL') || die();

/**
 * Returns a list of all assignments that have online text submission enabled for
 *  the given course id.
 */
function get_available_assignments($courseid)
{
    global $DB;
    

    $sql = 'SELECT DISTINCT asn.id, asn.name '
         . ' FROM mdl_assign asn'
         . ' INNER JOIN mdl_assign_plugin_config cfg on cfg.assignment = asn.id'
         . ' WHERE asn.course = ' . $courseid
         . ' AND cfg.plugin = "onlinetext"'
         . ' AND cfg.value = 1';

    // Submit the query
    $result = $DB->get_records_sql($sql);

    return $result;
}
