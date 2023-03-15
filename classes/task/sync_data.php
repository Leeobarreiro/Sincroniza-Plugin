<?php
namespace local_alunoinfo\task;

defined('MOODLE_INTERNAL') || die();

class sync_data extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('task_sync_data', 'local_alunoinfo');
    }

    public function execute() {
        // Call your function here
        xmldb_local_alunoinfo_cron();
    }
}
?>