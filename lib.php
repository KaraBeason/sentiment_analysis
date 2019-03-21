<?php

/**
 * Returns a list of all assignments that have associated online text submissions.
 */
function get_available_assignments($courseid, array $assignids = null)
{
    global $DB;

    $params = [ 'course' => $courseid ];

    // Fix up SQL for course assignments
    $sql = 'SELECT asn.id, name '
         . ' FROM mdl_assignsubmission_onlinetext t '
         . ' INNER JOIN mdl_assign asn ON t.assignment = asn.id '
           // join for the course in here
         . ' WHERE course = ' . $courseid;

    // Is there a set of assignments to filter further?
    if ($assignids) {
        // need to append to the where clause so only assign ids *IN* set will be returned
    }

    // Submit the query
    $result = $DB->get_records_sql($sql);

    // Kick 'em back
    return $result;

}
