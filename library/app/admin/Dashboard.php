<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Admin dashboard
 */

namespace app\admin;

use helpers\Utils;
use helpers\View;

class Dashboard {
    private $config;
    private View $view;
    private Utils $utils;
    private Header $header;
    private Footer $footer;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'admin/'._ADMIN_TEMPLATE.'/');

        $this->utils = new Utils;

        $this->header = new Header;
        $this->footer = new Footer;
    }

    public function main(): void
    {
        $this->header->set();

        $this->view->set_filenames(['dashboard' => 'dashboard.html']);

        $this->view->assign_vars([
            'ARTICLES_COUNT' => (new Articles)->countAll(),
            'SECTIONS_COUNT' => (new Sections)->countAll(),
            'AUTHORS_COUNT' => (new Authors)->countAll(),

            '_URL' => _URL.'admin/'
        ]);

        $this->view->pparse('dashboard');

        $this->footer->set();
    }
}