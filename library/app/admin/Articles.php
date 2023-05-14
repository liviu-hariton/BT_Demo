<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Admin articles section
 */

namespace app\admin;

use helpers\Utils;
use helpers\View;
use system\Article;
use system\Author;
use system\Section;
use Westsworld\TimeAgo;
use yidas\data\Pagination;

class Articles {
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

        $this->view->set_filenames(['articles' => 'articles.html']);

        $articles_count = (new Article)->countAll();

        if($articles_count > 0) {
            $paginate = new Pagination([
                'totalCount' => $articles_count
            ]);

            $pagination = \yidas\widgets\Pagination::widget([
                'pagination' => $paginate,
                'view' => $this->config->storage->layout.'admin/'._ADMIN_TEMPLATE.'/blocks/pagination.html'
            ]);

            $this->view->assign_block_vars("pagination", [
                'PAGINATION' => $pagination
            ]);

            $articles = (new Article)->getAll(['offset' => $paginate->offset, 'limit' => $paginate->limit]);

            foreach($articles as $article) {
                $section_data = (new Section)->getSection($article->idSection);

                $author = (new Author)->getAuthor($article->idAuthor);

                $this->view->assign_block_vars("item", [
                    'ID' => $article->idArticle,
                    'TITLE' => $article->title,
                    'NYT_URL' => $article->url,
                    'NYT_ID' => $article->id,
                    'LEAD_PARAGRAPH' => $article->lead_paragraph,
                    'SOURCE' => $article->source,
                    'IMAGE' => !is_null($article->image) ? $article->image : 'default.jpg',
                    'PUBLISHED' => (new TimeAgo(new TimeAgo\Translations\Ro()))->inWordsFromTS($article->published),
                    'PUBLISHED_FULL' => $this->utils->dateLiteral([
                        'timestamp' => $article->published,
                        'date_format' => 'EEEE, d MMMM Y @ HH:mm:ss',
                        'date_type' => 'LONG',
                        'time_type' => 'MEDIUM'
                    ]),
                    'CREATED' => (new TimeAgo(new TimeAgo\Translations\Ro()))->inWordsFromTS($article->created),
                    'CREATED_FULL' => $this->utils->dateLiteral([
                        'timestamp' => $article->created,
                        'date_format' => 'EEEE, d MMMM Y @ HH:mm:ss',
                        'date_type' => 'LONG',
                        'time_type' => 'MEDIUM'
                    ]),

                    'AUTHOR_ID' => $author->idAuthor ?? '',
                    'FIRSTNAME' => $author->firstname ?? '',
                    'MIDDLENAME' => $author->middlename ?? '',
                    'LASTNAME' => $author->lastname ?? '',

                    'SECTION_ID' => $section_data->idSection,
                    'SECTION_NAME' => $section_data->name,

                    '_URL' => _URL
                ]);

                if($article->idSubSection != '0') {
                    $subsection_data = (new Section)->getSubSection($article->idSubSection);

                    if($subsection_data->name != '') {
                        $this->view->assign_block_vars("item.subsection", [
                            'SUBSECTION_ID' => $subsection_data->idSubSection,
                            'SUBSECTION_NAME' => $subsection_data->name
                        ]);
                    }
                }
            }

            $this->view->set_filenames(array('items_list' => 'blocks/article_item.html'));

            $this->view->assign_var_from_handle("ITEMS_LIST", "items_list");
        } else {
            $this->view->assign_block_vars("no_items", []);
        }

        $this->view->assign_vars([
            'TOTAL' => $articles_count,

            '_URL' => _URL
        ]);

        $this->view->pparse('articles');

        $this->footer->set();
    }

    public function countAll(): int
    {
        return (new Article)->countAll();
    }
}