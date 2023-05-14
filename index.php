<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end section
 */

use helpers\Route;

global $config, $load_start;

include_once 'config.php';

if(!isset($_GET['page'])) {
    $_GET['page'] = '1';
}

// Load Front routes
(new Route)->deliver($config->front_routes);

// Take note of the ending time of the application
$load_end = microtime(true);

// Show the time it took to run and deliver an output
echo "<!-- Time to generate: ".number_format(round($load_end - $load_start, 5), 5, '.', ' ') . " s] -->";