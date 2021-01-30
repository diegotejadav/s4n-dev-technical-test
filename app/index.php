<?php

/**
 * Main file to execute the deliveries with each Dron
 * @author Diego Tejada <diegotejadav@gmail.com>
 */

include 'models/Dron.php';
include 'models/Util.php';

function start()
{
    $config = include('config/default.php');

    $drones = Util::getInputFiles();

    foreach ($drones as $inputName => $paths) {
        $dron = new Dron($paths, $config['dron_deliveries_per_time']);
        // var_dump($paths); // TODO: Clear this comment
        $outputFileName = str_replace('in', 'out', $inputName);
        $dron->startDelivery($outputFileName);
    }
}

start();

?>