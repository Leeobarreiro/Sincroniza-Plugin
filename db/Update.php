<?php
function xmldb_local_meuplugin_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Adiciona o campo 'carga_horaria' na tabela 'aluno_info'
    if ($oldversion < 2022031101) {
        $table = new xmldb_table('aluno_info');
        $field = new xmldb_field('carga_horaria', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'coorteid');

        // Adiciona o campo na tabela
        $dbman->add_field($table, $field);

        // Define a versão para a próxima atualização
        $upgradeversion = 2022031101;
        upgrade_plugin_savepoint(true, $upgradeversion);
    }

    // Adiciona o campo 'pontos' na tabela 'aluno_info'
    if ($oldversion < 2022031102) {
        $table = new xmldb_table('aluno_info');
        $field = new xmldb_field('pontos', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'carga_horaria');

        // Adiciona o campo na tabela
        $dbman->add_field($table, $field);

        // Define a versão para a próxima atualização
        $upgradeversion = 2022031102;
        upgrade_plugin_savepoint(true, $upgradeversion);
    }

    // Retorna true indicando que a atualização foi realizada com sucesso
    return true;
}
 ?>