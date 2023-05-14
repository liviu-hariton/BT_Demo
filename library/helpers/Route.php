<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * App router
 */

namespace helpers;

use app\front\NotFound;

class Route {
    public function __construct() {}

    public function deliver($routes): void
    {
        $routes = (array)$routes;

        $path_info = '/';

        if(!empty($_SERVER['PATH_INFO'])) {
            $path_info = $_SERVER['PATH_INFO'];
        } elseif (!empty($_SERVER['ORIG_PATH_INFO'])) {
            $path_info = $_SERVER['ORIG_PATH_INFO'];
        } else {
            if(!empty($_SERVER['REQUEST_URI'])) {
                $path_info = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
            }
        }

        $discovered_handler = null;
        $discovered_method = null;
        $regex_matches = [];

        $tokens = [
            ':string' => '([a-zA-Z0-9-_]+)',
            ':number' => '([0-9]+)',
            ':alpha'  => '([a-zA-Z-_]+)',
            ':filepath' => '([a-zA-Z0-9-_./]+)'
        ];

        foreach($routes as $pattern=>$handler) {
            $pattern = strtr($pattern, $tokens);

            if(preg_match('#^/?'.$pattern.'/?$#', $path_info, $matches)) {
                // Check if the Class and the Method are set
                if(strpos($handler, '@')) {
                    [$discovered_handler, $discovered_method] = explode("@", $handler);
                } else {
                    // else the default 'main()' Method in Class is used
                    $discovered_handler = $handler;
                    $discovered_method = 'main';
                }

                $regex_matches = $matches;
                break;
            }
        }

        // If no route is found and we're in the admin section then redirect to admin Dashboard section
        if(count($regex_matches) === 0 && preg_match("/admin\//", $_SERVER['REQUEST_URI'])) {
            (new Utils)->redirect('admin/');
        }

        $handler_instance = null;

        if($discovered_handler) {
            if(is_string($discovered_handler)) {
                $handler_instance = new $discovered_handler();
            } elseif (is_callable($discovered_handler)) {
                $handler_instance = $discovered_handler();
            }
        }

        if($handler_instance) {
            // Unset the URI regex statement (as defined in routes mapping) and reset the array's indexes
            unset($regex_matches[0]);

            if(isset($regex_matches[1])) {
                unset($regex_matches[1]);
            }

            // Pagination keyword is set, let's keep only the page number
            if(isset($regex_matches[4])) {
                unset($regex_matches[3], $regex_matches[4]);
            }

            $regex_matches = array_values($regex_matches);

            if(method_exists($handler_instance, $discovered_method)) {
                call_user_func(array(
                    $handler_instance,
                    $discovered_method
                ), $regex_matches);
            }
        } else {
            (new NotFound)->render();
        }
    }
}