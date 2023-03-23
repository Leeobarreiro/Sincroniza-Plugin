<?php
namespace local_sincroniza_plugin\task;

defined('MOODLE_INTERNAL') || die();

class sync_data extends \core\task\scheduled_task {

    /**
     * @return \lang_string|string
     */
    public function get_name(): string
    {
        return get_string('task_sync_data', 'local_sincroniza_plugin');
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        global $DB;

        // Get all enrolled courses for all users.
        $enrolments = $DB->get_recordset_sql("
        SELECT ue.userid, e.courseid, c.fullname
        FROM {user_enrolments} ue
        JOIN {enrol} e ON e.id = ue.enrolid
        JOIN {course} c ON c.id = e.courseid
        WHERE e.status = :status

    ", array('status' => ENROL_INSTANCE_ENABLED));

        // Iterate over enrolments to gather user data.
        foreach ($enrolments as $enrolment) {
            $userid = $enrolment->userid;
            $courseid = $enrolment->courseid;

            // Check if user is already synced with Aluno Info.
            if ($DB->record_exists('local_alunoinfo_data', array('userid' => $userid, 'courseid' => $courseid))) {
                continue;
            }

            // Gather user's progress data for this course.
            $course = new \stdClass();
            $course->id = $courseid;
            $progress = \core_progress\manager::get_course_progress($userid, $course);
            $completion = \core_completion\api::is_course_complete($userid, $course);

            // Get user's cohort information.
            $cohorts = implode(',', cohort_get_user_cohortids($userid));

            // Insert data into Aluno Info table.
            $data = new \stdClass();
            $data->userid = $userid;
            $data->courseid = $courseid;
            $data->coursename = $enrolment->fullname;
            $data->progress = $progress;
            $data->completion = $completion;
            $data->cohorts = $cohorts;

            // Check if additional fields are defined and populate them if they exist.
            $alunoinfoconfig = get_config('local_alunoinfo');
            if (!empty($alunoinfoconfig->cargahoraria_fieldname)) {
                $data->{$alunoinfoconfig->cargahoraria_fieldname} = get_course_cargahoraria($courseid);
            }
            if (!empty($alunoinfoconfig->pontos_fieldname)) {
                $data->{$alunoinfoconfig->pontos_fieldname} = get_course_pontos($courseid);
            }

            $DB->insert_record('local_alunoinfo_data', $data);
        }

        $enrolments->close();
    }
}
