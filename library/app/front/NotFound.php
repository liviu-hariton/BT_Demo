<?php
/**
 * SyncSHOP e-commerce platform
 * Author: SYNCDEV SRL <salut@syncshop.eu> @link https://www.syncshop.eu
 * (C) All rights reserved. Changing this code without the author's consent is strictly prohibited.
 */

namespace app\front;

use helpers\View;

class NotFound {
    private $config;
    private View $view;
    private Header $header;
    private Footer $footer;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'front/'._FRONT_TEMPLATE.'/');

        $this->header = new Header;
        $this->footer = new Footer;
    }

    public function render(): void
    {
        http_response_code(404);

        $this->header->set();

        $this->view->set_filenames(['404' => '404.html']);

        $this->view->assign_vars([

        ]);

        $this->view->pparse('404');

        $this->footer->set();
    }
}