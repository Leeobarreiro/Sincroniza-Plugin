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

defined('MOODLE_INTERNAL') || die();

function xmldb_local_sincroniza_plugin_install() {
    global $DB, $CFG, $OUTPUT;

    require_once($CFG->libdir . '/ddl/database_manager.php');
    $dbman = $DB->get_manager();

    $table = new xmldb_table('local_alunoinfo_data');

    $field_id = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $field_userid = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $field_courseid = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $field_coursename = new xmldb_field('coursename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $field_progress = new xmldb_field('progress', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $field_completion = new xmldb_field('completion', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
    $field_cohorts = new xmldb_field('cohorts', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

    $table->addField($field_id);
    $table->addField($field_userid);
    $table->addField($field_courseid);
    $table->addField($field_coursename);
    $table->addField($field_progress);
    $table->addField($field_completion);
    $table->addField($field_cohorts);

    $key = new xmldb_key('primary');
    $key->setFields(array('id'));
    $key->setType(XMLDB_KEY_PRIMARY);
    $table->addKey($key);

    $dbman = $DB->get_manager();

    // Cria a tabela e armazena o resultado na variável $status
    $status = $dbman->create_table($table);

    // Verifica se a tabela foi criada com sucesso
    if ($status) {
        // A tabela foi criada com sucesso, faça algo aqui se necessário
    } else {
        // A tabela não foi criada com sucesso, trate o erro aqui se necessário
    }
}
?>
