<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API main section
 */

namespace app\api;

use helpers\Utils;

class Api {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    private function responseMessages() {
        return [
            'nohandler' => 'You need to provide a handler',
            'nomethod' => 'You need to provide a method',
            'handlerforbidden' => 'The specified handler is not permitted',
            'methodforbidden' => 'The specified method is not permitted',
            'noaccessmethod' => 'The access method is not allowed',
            'nodate' => 'You need to provide a date',
            'nodateformat' => 'You need to provide a valid date format: dd.mm.yyyy',
            'invaliddate' => 'The provided date is not valid',
            'nonewsid' => 'You need to provide a news ID',
            'notfound' => 'The resource is not available',
            'nocriteria' => 'You need to provide at least a keyword',
            'nosectionid' => 'You need to provide a section ID',
            'noauthorid' => 'You need to provide an author ID',
            'nofield' => 'You need to provide a setting field',
            'pageno' => 'Invalid page number',
            'noslug' => 'You need to provide a slug'
        ];
    }

    public function response($code, $message) {
        http_response_code($code);

        $messages = $this->responseMessages();

        $output = [
            'code' => $code,
            'error' => $messages[$message]
        ];

        return json_encode($output);
    }
}