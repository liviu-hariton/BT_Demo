<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * App configuration
 */

// Take note of the starting time of the application
$load_start = microtime(true);

// Set locale and timezone
const _TIME_ZONE = 'Europe/Bucharest';
const _LOCALE = 'ro_RO';
const _LOCALE_SHORT = 'ro';

date_default_timezone_set(_TIME_ZONE);
ini_set('date.timezone', _TIME_ZONE);
setlocale(LC_TIME, "");
setlocale(LC_TIME, _LOCALE);

// Set the base path on server
const _ROOT = __DIR__.'/';

// Gather third-party libraries
include_once _ROOT.'/library/vendor/illuminate/database/vendor/autoload.php';
include_once _ROOT.'/library/vendor/timeago/vendor/autoload.php';
include_once _ROOT.'/library/vendor/json_machine/vendor/autoload.php';
include_once _ROOT.'/library/vendor/guzzle/autoload.php';
include_once _ROOT.'/library/vendor/pagination/vendor/autoload.php';

include_once _ROOT.'/library/vendor/btdemo-sdk/BT_Demo_API.php';

// Setup auto-loading
spl_autoload_register(static function($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);

    if(
        !str_contains($className, "Illuminate") && // DB library
        !str_contains($className, "Capsule") && // DB library
        !str_contains($className, "Doctrine") // DB library
    ) {
        include_once _ROOT.'/library/'.$className.'.php';
    }
});


// Load App's credentials and settings data
$ini_data = parse_ini_file("app.ini");

// Replace the placeholders with their actual value
function appIniVariables(&$item): void
{
    $item = str_replace("%%_ROOT%%", _ROOT, $item);
}

array_walk_recursive($ini_data, "appIniVariables");

$config = json_decode(json_encode($ini_data));

// Set-up error reporting and logging
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", $config->storage->logs."errors.log");

// Set the main App URL
define('_URL', (!empty($_SERVER['HTTPS']) ? 'https' : 'http').'://'.(array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '').'/');

// Initiate DB object
include_once _ROOT.'/library/vendor/illuminate/database/capsule.php';

use Illuminate\Database\Capsule\Manager as DB;

// Set site-wide settings as configured in the database
$settings = DB::table('settings')->get();

foreach($settings as $setting) {
    define('_'.strtoupper($setting->name), $setting->value);
}