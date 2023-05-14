<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end header
 */

namespace app\front;

use BT_Demo\BT_Demo_API;
use helpers\Utils;
use helpers\View;
use system\Language;

class Header {
    /**
     * @var array|mixed
     */
    private $config;
    private View $view;
    private Utils $utils;
    private BT_Demo_API $btdemo;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'front/'._FRONT_TEMPLATE.'/');

        $this->utils = new Utils;

        $this->btdemo = new BT_Demo_API;
    }

    public function set($data = []): void
    {
        $this->view->set_filenames(['header' => 'header.html']);

        $categories = $this->btdemo->decodeJsonResponse($this->btdemo->getAllSections());

        foreach($categories['sections'] as $category) {
            $this->view->assign_block_vars("category", [
                'NAME' => $category['name'],
                'SLUG' => $category['alias']
            ]);
        }

        $this->view->assign_vars([
            '_URL' => _URL
        ]);

        $this->view->pparse('header');
    }
}