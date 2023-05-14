<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Front-end Author's page
 */

namespace app\front;

use BT_Demo\BT_Demo_API;
use helpers\Utils;
use helpers\View;
use Westsworld\TimeAgo;

class Category {
    private $config;
    private View $view;
    private Utils $utils;
    private Header $header;
    private Footer $footer;
    private BT_Demo_API $btdemo;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->view = new View($this->config->storage->layout.'front/'._FRONT_TEMPLATE.'/');

        $this->utils = new Utils;

        $this->header = new Header;
        $this->footer = new Footer;

        $this->btdemo = new BT_Demo_API;
    }

    public function main(): void
    {
        $this->render();
    }

    private function getSlug() {
        $uri_parts = explode("/", $_SERVER['REQUEST_URI']);

        return $uri_parts[1];
    }

    public function render(): void
    {
        $this->header->set();

        $this->view->set_filenames(['category' => 'category.html']);

        $section = $this->btdemo->decodeJsonResponse($this->btdemo->getSectionBySlug($this->getSlug()));

        if(isset($section['idSection'])) {
            $latest_article = $this->btdemo->decodeJsonResponse($this->btdemo->getSectionLatestArticle($section['idSection']));
            $latest_author = $this->btdemo->decodeJsonResponse($this->btdemo->getAuthor($latest_article['idAuthor']));

            $this->view->assign_block_vars("latest", [
                'LATEST_TITLE' => $latest_article['title'],
                'LATEST_LEAD_PARAGRAPH' => $latest_article['lead_paragraph'],
                'LATEST_IMAGE' => $latest_article['image'] != '' ? $latest_article['image'] : 'default.jpg',

                'LATEST_AUTHOR' => $latest_author['firstname'].' '.$latest_author['middlename'].' '.$latest_author['lastname'],
                'LATEST_AUTHOR_SLUG' => $latest_author['alias'],

                'LATEST_SECTION' => $section['name'],
                'LATEST_SECTION_SLUG' => $section['alias']
            ]);


            $articles = $this->btdemo->decodeJsonResponse($this->btdemo->getSectionArticles($section['idSection']));

            foreach($articles['news'] as $article) {
                $section_data = $this->btdemo->decodeJsonResponse($this->btdemo->getSection($article['idSection']));
                $author = $this->btdemo->decodeJsonResponse($this->btdemo->getAuthor($article['idAuthor']));

                $this->view->assign_block_vars("item", [
                    'ID' => $article['idArticle'],
                    'TITLE' => $article['title'],
                    'NYT_URL' => $article['url'],
                    'NYT_ID' => $article['id'],
                    'LEAD_PARAGRAPH' => $article['lead_paragraph'],
                    'SOURCE' => $article['source'],
                    'IMAGE' => !is_null($article['image']) ? $article['image'] : 'default.jpg',
                    'PUBLISHED' => (new TimeAgo(new TimeAgo\Translations\Ro()))->inWordsFromTS($article['published']),
                    'PUBLISHED_FULL' => $this->utils->dateLiteral([
                        'timestamp' => $article['published'],
                        'date_format' => 'EEEE, d MMMM Y @ HH:mm:ss',
                        'date_type' => 'LONG',
                        'time_type' => 'MEDIUM'
                    ]),
                    'CREATED' => (new TimeAgo(new TimeAgo\Translations\Ro()))->inWordsFromTS($article['created']),
                    'CREATED_FULL' => $this->utils->dateLiteral([
                        'timestamp' => $article['created'],
                        'date_format' => 'EEEE, d MMMM Y @ HH:mm:ss',
                        'date_type' => 'LONG',
                        'time_type' => 'MEDIUM'
                    ]),

                    'AUTHOR_SLUG' => $author['alias'] ?? '',
                    'AUTHOR_FIRSTNAME' => $author['firstname'] ?? '',
                    'AUTHOR_MIDDLENAME' => $author['middlename'] ?? '',
                    'AUTHOR_LASTNAME' => $author['lastname'] ?? '',

                    'SECTION_SLUG' => $section_data['alias'],
                    'SECTION_NAME' => $section_data['name']
                ]);
            }
        } else {
            $this->view->assign_block_vars("no_items", []);
        }

        $this->view->assign_vars([
            'SECTION_NAME' => $section['name'],

            '_URL' => _URL
        ]);

        $this->view->pparse('category');

        $this->footer->set();
    }
}