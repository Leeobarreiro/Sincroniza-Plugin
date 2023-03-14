<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Returns an array with all enrolled courses for a given user.
 *
 * @param int $userid The user ID.
 * @return array An array with course IDs.
 */
function get_enrolled_courses($userid) {
    global $DB;

    $sql = "SELECT ue.courseid
            FROM {user_enrolments} ue
            JOIN {enrol} e ON e.id = ue.enrolid
            WHERE ue.status = :status AND e.status = :estatus AND ue.userid = :userid";
    $params = [
        'status' => ENROL_USER_ACTIVE,
        'estatus' => ENROL_INSTANCE_ENABLED,
        'userid' => $userid
    ];
    return $DB->get_fieldset_sql($sql, $params);
}

/**
 * Returns an array with the completion status of all courses for a given user.
 *
 * @param int $userid The user ID.
 * @param array $courseids An array with course IDs.
 * @return array An array with the completion status of each course.
 */
function get_completion_status($userid, $courseids) {
    global $DB;

    $completion = new completion_info();
    $statuses = [];
    foreach ($courseids as $courseid) {
        $statuses[$courseid] = $completion->is_enabled_for_course($courseid) ?
            $completion->get_status($userid, $courseid) : COMPLETION_INCOMPLETE;
    }
    return $statuses;
}

/**
 * Returns an array with the cohorts the user belongs to.
 *
 * @param int $userid The user ID.
 * @return array An array with cohort IDs.
 */
function get_user_cohorts($userid) {
    global $DB;

    $sql = "SELECT c.id
            FROM {cohort_members} cm
            JOIN {cohort} c ON c.id = cm.cohortid
            WHERE cm.userid = :userid";
    $params = [
        'userid' => $userid
    ];
    return $DB->get_fieldset_sql($sql, $params);
}

/**
 * Returns an array with the additional information for a given user.
 *
 * @param int $userid The user ID.
 * @return array An array with the additional information for the user.
 */
function get_user_additional_info($userid) {
    global $DB;

    $sql = "SELECT c.courseid, gi.name, gi.value
            FROM {course_completions} cc
            JOIN {grade_grades} gg ON gg.itemid = cc.id
            JOIN {grade_items} gi ON gi.id = gg.itemid AND gi.itemtype = 'course'
            WHERE cc.userid = :userid AND gi.name IN ('Carga horária', 'Pontos')";
    $params = [
        'userid' => $userid
    ];
    $results = $DB->get_records_sql($sql, $params);

    $additional_info = [];
    foreach ($results as $result) {
        $additional_info[$result->courseid][$result->name] = $result->value;
    }
    return $additional_info;
}

?>