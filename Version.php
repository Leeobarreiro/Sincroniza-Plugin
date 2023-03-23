<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_local_sincroniza_plugin_install() {
    global $DB;
    require_once($DB->dir . '/local/sincroniza_plugin/db/install.php');
    xmldb_local_sincroniza_plugin_install();
}

$plugin->version   = 2023030101;
$plugin->requires  = 2020110900;
$plugin->component = 'local_sincroniza_plugin';
$plugin->release   = '1.0.0';
$plugin->maturity  = MATURITY_STABLE;

?>