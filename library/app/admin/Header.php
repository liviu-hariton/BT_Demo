<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end header
 */

namespace app\admin;

use helpers\Utils;
use helpers\View;

class Header {
    /**
     * @var array|mixed
     */
    private $config;
    private View $view;
    private Utils $utils;
    private string $current_section;
    private string $current_subsection;
    private $current_url;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'admin/'._ADMIN_TEMPLATE.'/');

        $this->utils = new Utils;

        $this->current_url = $this->utils->getCurrentURI();
        $this->current_section = $this->setCurrentSection();
        $this->current_subsection = $this->setCurrentSubSection();
    }

    private function getUrlData(): array
    {
        $the_url = $this->current_url;

        $data = [
            'url' => $the_url
        ];

        if($this->current_url == 'admin/') { // we are home
            $data['section'] = '';
            $data['subsection'] = '';
        } else {
            $the_url_data = explode("/", $the_url);

            unset($data[0]); // remove the "admin" folder from path

            $data['section'] = $the_url_data[1] ?? '';

            if(!empty($the_url_data[2])) {
                if(str_contains($the_url_data[2], '?')) {
                    $the_url_data_parts = explode('?', $the_url_data[2]);

                    $data['subsection'] = $the_url_data_parts[0];
                } else {
                    $data['subsection'] = $the_url_data[2];
                }
            } else {
                $data['subsection'] = '';
            }
        }

        return $data;
    }

    private function setCurrentSection(): string|null
    {
        $url_data = $this->getUrlData();

        return $url_data['section'];
    }

    public function getCurrentSection(): string
    {
        return $this->current_section;
    }

    private function setCurrentSubSection(): string
    {
        $url_data = $this->getUrlData();

        return $url_data['subsection'];
    }

    public function getCurrentSubSection(): string
    {
        return $this->current_subsection;
    }

    private function adminSections(): array
    {
        return [
            [
                'url' => '',
                'title' => 'Dashboard',
                'icon' => 'home'
            ],
            [
                'url' => 'articles',
                'title' => 'Articole',
                'icon' => 'file'
            ],
            [
                'url' => 'sections',
                'title' => 'Sectiuni',
                'icon' => 'layers'
            ],
            [
                'url' => 'authors',
                'title' => 'Autori',
                'icon' => 'users'
            ],
            [
                'url' => 'settings',
                'title' => 'Configurari',
                'icon' => 'settings'
            ]
        ];
    }

    public function set($data = []): void
    {
        $this->view->set_filenames(['header' => 'header.html']);

        // build the main menu
        foreach($this->adminSections() as $section) {
            $this->view->assign_block_vars("section", [
                'TITLE' => $section['title'],
                'ICON' => $section['icon'],
                'URL' => $section['url'],
                'ACTIVE_CSS' => $section['url'] == $this->current_section ? 'active' : ''
            ]);
        }

        $this->view->assign_vars([
            '_URL' => _URL.'admin/'
        ]);

        $this->view->pparse('header');
    }
}