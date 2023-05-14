<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API authors section
 */

namespace app\api;

use helpers\Utils;
use system\Author;

class Authors {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    public function getall(): void
    {
        $items = (new Author)->getAll();

        $result = [
            'totalResults' => count($items),
            'authors' => $items
        ];

        echo json_encode($result);
    }

    public function getbyid($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        $item = (new Author)->getAuthor($input);

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

        $items = (new Author)->search($input);

        $result = [
            'keyword' => $input,
            'totalResults' => count($items),
            'authors' => $items
        ];

        echo json_encode($result);
    }

    public function getbyslug($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'noslug');
            exit;
        }

        $item = (new Author)->getAuthorBySlug($input);

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }
}