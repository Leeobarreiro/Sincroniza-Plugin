<?php

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => '\local_sincroniza_plugin\task\sync_data',
        'blocking' => 0,
        'minute' => '*/15', // a cada 15 minutos
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0
    )
);

?>