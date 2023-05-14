<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end footer
 */

namespace app\front;

use helpers\View;

class Footer {
    private $config;
    private View $view;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'front/'._FRONT_TEMPLATE.'/');
    }

    public function set($data = []): void
    {
        $this->view->set_filenames(['footer' => 'footer.html']);

        $this->view->assign_vars([
            'YEAR' => date('Y'),

            '_URL' => _URL
        ]);

        $this->view->pparse('footer');
    }
}