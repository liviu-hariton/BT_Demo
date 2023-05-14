<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Fetch content - Cron job
 * (currently set to run at the beginning of every hour)
 */

use app\admin\Settings;

set_include_path(dirname(__DIR__).'/');
set_include_path(get_include_path() . PATH_SEPARATOR . str_replace("cron", "", dirname(__DIR__)));
include_once 'config.php';

(new Settings)->importContent();