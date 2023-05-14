<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end footer
 */

namespace app\admin;

use helpers\View;

class Footer {
    private $config;
    private View $view;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'admin/'._ADMIN_TEMPLATE.'/');
    }

    public function set($data = []): void
    {
        $this->view->set_filenames(['footer' => 'footer.html']);

        $this->view->assign_vars([
            '_URL' => _URL
        ]);

        $this->view->pparse('footer');
    }
}