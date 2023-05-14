<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API authors section
 */

namespace app\api;

use helpers\Utils;

class Settings {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    private function allowedFields() {
        return [
            'front_template', 'admin_template', 'content_source', 'nyt_endpoint', 'nyt_apikey', 'content_file_select', 'per_page', 'per_page_front'
        ];
    }

    public function get($field): void
    {
        if(empty($field)) {
            echo (new Api)->response(500, 'nofield');
            exit;
        }

        if(!in_array($field, $this->allowedFields())) {
            echo (new Api)->response(404, 'notfound');
            exit;
        }

        $result = [
            'field' => $field,
            'value' => (new \system\Settings)->getSettingValue($field)
        ];

        echo json_encode($result);
    }
}