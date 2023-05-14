<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * API articles section
 */

namespace app\api;

use helpers\Utils;
use system\Article;
use system\Author;
use system\Section;
use yidas\data\Pagination;

class Articles {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    public function getall($page_no = ''): void
    {
        if(!empty($page_no) && !ctype_digit($page_no)) {
            echo (new Api)->response(500, 'pageno');
            exit;
        }

        $items_count = (new Article)->countAll();

        $page_no = !empty($page_no) ? $page_no : '1';

        $_GET['page'] = $page_no;

        $paginate = new Pagination([
            'totalCount' => $items_count
        ]);

        $items = (new Article)->getAll(['offset' => $paginate->offset, 'limit' => $paginate->limit]);

        $result = [
            'totalResults' => $items_count,
            'current_page' => $page_no,
            'news' => $items
        ];

        echo json_encode($result);
    }

    public function getbydate($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nodate');
            exit;
        }

        if(!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.([0-9]{4})$/", $input)) {
            echo (new Api)->response(500, 'nodateformat');
            exit;
        }

        $date_parts = explode(".", $input);

        if(!checkdate($date_parts[1], $date_parts[0], $date_parts[2])) {
            echo (new Api)->response(500, 'invaliddate');
            exit;
        }

        $items = (new Article)->getAllByDate($date_parts);

        $result = [
            'date' => $input,
            'totalResults' => count($items),
            'news' => $items
        ];

        echo json_encode($result);
    }

    public function getbyid($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nonewsid');
            exit;
        }

        $item = (new Article)->get($input);

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

        $items = (new Article)->search($input);

        $result = [
            'keyword' => $input,
            'totalResults' => count($items),
            'news' => $items
        ];

        echo json_encode($result);
    }

    public function getbysection($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        $section = (new Section)->getSection($input);

        if(is_null($section)) {
            echo (new Api)->response(404, 'notfound');
            exit;
        }

        $items = (new Article)->getBySection($input);

        $result = [
            'section_id' => $input,
            'section_name' => $section->name,
            'totalResults' => count($items),
            'news' => $items
        ];

        echo json_encode($result);
    }

    public function getbyauthor($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        $author = (new Author)->getAuthor($input);

        if(is_null($author)) {
            echo (new Api)->response(404, 'notfound');
            exit;
        }

        $items = (new Article)->getByAuthor($input);

        $result = [
            'author_id' => $input,
            'author' => $author->firstname.' '.$author->middlename.' '.$author->lastname,
            'totalResults' => count($items),
            'news' => $items
        ];

        echo json_encode($result);
    }

    public function getlatest(): void
    {
        $item = (new Article)->getLatest();

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }

    public function getlatestinsection($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'nosectionid');
            exit;
        }

        $section = (new Section)->getSection($input);

        if(is_null($section)) {
            echo (new Api)->response(404, 'notfound');
            exit;
        }

        $item = (new Article)->getLatestInSection($input);

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }

    public function getlatestbyauthor($input): void
    {
        if(empty($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        if(!ctype_digit($input)) {
            echo (new Api)->response(500, 'noauthorid');
            exit;
        }

        $author = (new Author)->getAuthor($input);

        if(is_null($author)) {
            echo (new Api)->response(404, 'notfound');
            exit;
        }

        $item = (new Article)->getLatestByAuthor($input);

        if(!is_null($item)) {
            echo json_encode($item);
        } else {
            echo (new Api)->response(404, 'notfound');
            exit;
        }
    }
}