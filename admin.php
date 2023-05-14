<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Admin section
 */

// Prevent browser cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate");
header("Pragma: no-cache");

use helpers\Route;
use system\App;

global $config, $load_start;

include_once 'config.php';

// Process POST requests
if(isset($_POST['save_form_data'])) {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        (new App)->postAdminRequest();
    } else {
        (new Utils)->redirect('admin/');
    }
}


// Load Front routes
(new Route)->deliver($config->admin_routes);

// Take note of the ending time of the application
$load_end = microtime(true);

// Show the time it took to run and deliver an output
echo "<!-- Time to generate: ".number_format(round($load_end - $load_start, 5), 5, '.', ' ') . " s] -->";