<?php
/**
 * Aluno Info Web Service.
 *
 * @package    local_alunoinfo
 * @copyright  Copyright (c) 2023 Leonardo Barreiro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define que este arquivo é um script do Moodle.
define('NO_MOODLE_COOKIES', true);
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/moodlelib.php');

// Define o contexto padrão do sistema como contexto de segurança para o webservice.
$systemcontext = context_system::instance();
require_capability('moodle/webservice:createtoken', $systemcontext);

// Define as informações do webservice.
$services = array(
    'AlunoInfo' => array(
        'functions' => array('alunoinfo_get_data'),
        'requiredcapability' => 'local/alunoinfo:view',
        'restrictedusers' => 0,
        'enabled' => 1,
    ),
);

// Define as funções do webservice.
$functions = array(
    'alunoinfo_get_data' => array(
        'classname' => 'local_alunoinfo_external',
        'methodname' => 'get_aluno_info_data',
        'classpath' => 'local/alunoinfo/external.php',
        'description' => 'Retorna dados do Aluno Info para um usuário específico.',
        'type' => 'read',
    ),
);

// Registra os serviços e as funções do webservice.
foreach ($services as $name => $service) {
    $serviceid = $DB->insert_record('external_services', array(
        'name' => $name,
        'enabled' => $service['enabled'],
        'requiredcapability' => $service['requiredcapability'],
        'restrictedusers' => $service['restrictedusers'],
        'timemodified' => time(),
    ));

    foreach ($service['functions'] as $functionname) {
        if (isset($functions[$functionname])) {
            $function = $functions[$functionname];
            $functionid = $DB->insert_record('external_functions', array(
                'name' => $functionname,
                'classname' => $function['classname'],
                'methodname' => $function['methodname'],
                'classpath' => $function['classpath'],
                'description' => $function['description'],
                'type' => $function['type'],
                'externalserviceid' => $serviceid,
                'requiredcapability' => $service['requiredcapability'],
                'restrictedusers' => $service['restrictedusers'],
                'timemodified' => time(),
            ));
        }
    }
}

// Define a classe que contém a função do webservice.
class local_alunoinfo_external extends external_api {

    /**
     * Retorna dados do Aluno Info para um usuário específico.
     *
     * @param string $username Nome do usuário.
     * @return array
     */
    public static function get_aluno_info_data($username) {
        global $DB;
    
        // Obtém o ID do usuário com base no nome de usuário.
        $user = $DB->get_record('user', array('username' => $username));
        if (!$user) {
            throw new external_value_exception('user_not_found', 'Usuário não encontrado.');
        }
    
        // Obtém os dados do aluno com base no ID do usuário.
        $aluno_info = $DB->get_record('aluno_info', array('userid' => $user->id));
        if (!$aluno_info) {
            throw new external_value_exception('aluno_info_not_found', 'Informações do Aluno Info não encontradas.');
        }
    
        // Obtém o ID do curso do aluno.
        $courseid = get_config('local_alunoinfo', 'courseid');
        if (!$courseid) {
            throw new external_value_exception('course_not_found', 'ID do curso não encontrado.');
        }
    
        // Obtém a inscrição do aluno no curso.
        $enrollment = $DB->get_record('enrol', array('courseid' => $courseid, 'userid' => $user->id));
        if (!$enrollment) {
            throw new external_value_exception('enrollment_not_found', 'Inscrição do aluno não encontrada.');
        }
    
        // Obtém o contexto da inscrição do aluno.
        $context = context_course::instance($courseid);
    
        // Verifica se o usuário tem permissão para visualizar notas do curso.
        if (!has_capability('moodle/grade:viewall', $context, $user->id)) {
            throw new external_value_exception('no_permission', 'Usuário não tem permissão para visualizar notas.');
        }
    
        // Obtém as notas do aluno no curso.
        $gradebook = new gradebook(array('userid' => $user->id, 'courseid' => $courseid));
        $grades = $gradebook->get_grade_items_for_user();
    
        // Obtém o nome do usuário.
        $name = fullname($user);
    
        // Formata os dados do aluno em um array.
        $data = array(
            'name' => $name,
            'username' => $user->username,
            'course' => array(
                'id' => $courseid,
                'grades' => array(),
            ),
        );
    
        // Adiciona as notas do aluno no array de dados.
        foreach ($grades as $grade) {
            if ($grade->is_hidden() || $grade->is_outcome()) {
                continue;
            }
    
            $item = array(
                'id' => $grade->id,
                'name' => $grade->get_name(),
                'value' => $gradebook->get_grade($grade->id),
                'range' => $grade->get_grade_display_range(),
                'percentage' => $gradebook->get_grade_percentage($grade->id),
            );
    
            $data['course']['grades'][] = $item;
        }
    
        // Retorna os dados do aluno.
        $data = array(
            'nome' => $user->firstname . ' ' . $user->lastname,
            'curso' => $aluno_info->curso,
            'turma' => $aluno_info->turma,
            'periodo' => $aluno_info->periodo,
        );
    
        return $data;
    }
}

?>
    
