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

    // TODO: The drones deliveries should be called with some parallel processing, so 
    // they can run simultaneously
    foreach ($drones as $fileName => $paths) {
        $outputFileName = Util::outputFilename($fileName);
        $dron = new Dron($paths, $config['dron_deliveries_per_time'], $config['max_coverage'], $outputFileName);
        $dron->startDelivery();
        unset($dron);
    }

    echo("Finished deliveries");
}

start();

?>