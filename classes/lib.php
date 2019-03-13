<?php
namespace block_sentimentanalysis;

abstract class lib
{
    /**
     * Returns a list of all assignments that have associated online text submissions.
     */
    public static function get_available_assignments()
    {
        global $DB;
        $result = $DB->get_records_sql('SELECT *
                                        FROM mdl_assignsubmission_onlinetext t
                                        INNER JOIN mdl_assign asn ON t.assignment = asn.id');
        $assignments = array();
        foreach ($result as $row)
        {
            $assignments[$row->id] = $row->name;
        }
        return $assignments;
    }
}