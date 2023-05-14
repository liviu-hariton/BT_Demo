<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API Section
 */

use app\api\Api;

include_once 'config.php';

// The output is JSON
header("Content-type: application/json; charset=UTF-8");

/**
 * Check if used access method is allowed
 * we accept only GET at this point
 */
$allowed_access_methods = [
    'GET'
];

if(!in_array($_SERVER['REQUEST_METHOD'], $allowed_access_methods)) {
    echo (new Api)->response(403, 'noaccessmethod');
}

$url_parts = explode("/", $_SERVER['REQUEST_URI']);

// We don't need the first two elements
unset($url_parts[0], $url_parts[1]);

/**
 * Check to see if we have defined a handler and a method
 * $url_parts[2] - the handler
 * $url_parts[3] - the method
 */
if(empty($url_parts[2])) {
    echo (new Api)->response(404, 'nohandler');
    exit;
} else {
    if(empty($url_parts[3])) {
        echo (new Api)->response(404, 'nomethod');
        exit;
    }
}

/**
 * $url_parts[2] - the handler
 * $url_parts[3] - the method
 * $url_parts[4] - the resource
 */
$handler = $url_parts[2];
$method = $url_parts[3];
$resource = $url_parts[4] ?? null;

$allowed_handlers = [
    'articles', 'sections', 'authors', 'settings'
];

$allowed_methods = [
    'getall', 'getbydate', 'getbyid', 'search', 'getbysection', 'getbyauthor', 'get', 'getlatest', 'getlatestinsection', 'getlatestbyauthor', 'getbyslug'
];

/**
 * Check to handlers and methods whitelists
 */
if(!in_array($handler, $allowed_handlers)) {
    echo (new Api)->response(403, 'handlerforbidden');
    exit;
} else {
    if(!in_array($method, $allowed_methods)) {
        echo (new Api)->response(403, 'methodforbidden');
        exit;
    }
}

// Initialize and run
$handler_name = "app\api\\".ucwords($handler);
$handler_obj = new $handler_name;

call_user_func_array([$handler_obj, $method], [$resource]);