<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API sections section
 */

namespace app\api;

use helpers\Utils;
use system\Section;

class Sections {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    public function getall(): void
    {
        $items = (new Section)->getAllSections();

        $result = [
            'totalResults' => count($items),
            'sections' => $items
        ];

        echo json_encode($result);
    }

    public function getbyid($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        $item = (new Section)->getSection($input);

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }

    public function search($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nocriteria');
            exit;
        }

        $input = urldecode($input);

        $items = (new Section)->search($input);

        $result = [
            'keyword' => $input,
            'totalResults' => count($items),
            'sections' => $items
        ];

        echo json_encode($result);
    }

    public function getbyslug($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'noslug');
            exit;
        }

        $item = (new Section)->getSectionBySlug($input);

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }
}