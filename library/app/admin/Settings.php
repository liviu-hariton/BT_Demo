<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Admin settings section
 */

namespace app\admin;

use helpers\Upload;
use helpers\Utils;
use helpers\View;
use system\App;
use system\Article;
use system\Author;
use system\Section;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;

class Settings {
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

        $this->view->set_filenames(['settings' => 'settings.html']);

        if(isset($_GET['success'])) {
            $this->view->assign_block_vars("success", []);
        }

        if(isset($_GET['filetypeerror'])) {
            $this->view->assign_block_vars("filetypeerror", []);
        }

        /**
         * List all available front-end templates
         */
        $front_templates = (new App)->getFrontTemplates();

        foreach($front_templates as $front_template) {
            $this->view->assign_block_vars("front_template", [
                'NAME' => $front_template,
                'SEL' => $front_template === _FRONT_TEMPLATE ? 'selected="selected"' : ''
            ]);
        }

        /**
         * List all available admin templates
         */
        $admin_templates = (new App)->getAdminTemplates();

        foreach($admin_templates as $admin_template) {
            $this->view->assign_block_vars("admin_template", [
                'NAME' => $admin_template,
                'SEL' => $admin_template === _ADMIN_TEMPLATE ? 'selected="selected"' : ''
            ]);
        }

        /**
         * List all previously uploaded content files
         */
        $files = (new App)->getContentFiles();

        if(count($files) > 0) {
            $file_upload_css = 'style="display:none;"';
            $file_select_css = '';

            foreach($files as $file) {
                $this->view->assign_block_vars("content_file_select", [
                    'FILE' => $file['file'],
                    'SIZE' => $file['size'],
                    'CREATED' => $file['created'],
                    'SEL' => $file['file'] === _CONTENT_FILE_SELECT ? 'selected="selected"' : ''
                ]);
            }
        } else {
            $file_upload_css = '';
            $file_select_css = 'style="display:none;"';
        }

        $this->view->assign_vars([
            'SOURCE_LOCAL_CHK' => _CONTENT_SOURCE == 'local' ? 'checked="checked"' : '',
            'SOURCE_LOCAL_CSS' => _CONTENT_SOURCE == 'local' ? '' : 'style="display:none;"',
            'SOURCE_REMOTE_CHK' => _CONTENT_SOURCE == 'remote' ? 'checked="checked"' : '',
            'SOURCE_REMOTE_CSS' => _CONTENT_SOURCE == 'remote' ? '' : 'style="display:none;"',
            'FILE_UPLOAD_CSS' => $file_upload_css,
            'FILE_SELECT_CSS' => $file_select_css,

            '_NYT_ENDPOINT' => _NYT_ENDPOINT,
            '_NYT_APIKEY' => _NYT_APIKEY,

            '_PER_PAGE' => _PER_PAGE,
            '_PER_PAGE_FRONT' => _PER_PAGE_FRONT,

            '_URL' => _URL.'admin/'
        ]);

        $this->view->pparse('settings');

        $this->footer->set();
    }

    public function saveOverallSettings(): void
    {
        (new \system\Settings)->saveOverallSettings($_POST);

        (new Utils)->redirect('admin/settings/?success');
    }

    public function saveContentSettings(): void
    {
        switch($_POST['content_source']) {
            case "local":
                unset($_POST['nyt_endpoint'], $_POST['nyt_apikey']);

                if($_FILES['json_file']['error'] == '0' && $_FILES['json_file']['size'] > 0) {
                    // In a real world, we should check if the actual file content is JSON also
                    if(!str_contains($_FILES['json_file']['name'], "json")) {
                        $this->utils->redirect('admin/settings/?filetypeerror');
                    } else {
                        (new Upload)->file(
                            [
                                'input' => 'json_file',
                                'path' => $this->config->storage->files
                            ]
                        );
                    }
                }

                (new \system\Settings)->saveLocalContentSettings($_POST);
                break;
            case "remote":
                unset($_POST['content_file_select'], $_POST['nyt_apikey']);

                (new \system\Settings)->saveRemoteContentSettings($_POST);
                break;
        }

        if(isset($_POST['process_content_now']) && $_POST['process_content_now'] == '1') {
            $this->importContent();
        }

        (new Utils)->redirect('admin/settings/?success');
    }

    public function importContent(): void
    {
        switch((new \system\Settings)->getSettingValue('content_source')) {
            case "local":
                $this->importLocalContent();
                break;
            case "remote":
                $this->importRemoteContent();
                break;
        }
    }

    private function setSectionId($input) {
        $check = (new Section)->getSectionByName($input);

        if(!is_null($check)) {
            $section_id = $check->idSection;
        } else {
            $section_data = [
                'name' => $input,
                'alias' => $this->utils->safeLink($input)
            ];

            $section_id = (new Section)->addSection($section_data);
        }

        return $section_id;
    }

    private function setSubSectionId($section_id, $input) {
        $check = (new Section)->getSubSectionByName($section_id, $input);

        if(!is_null($check)) {
            $subsection_id = $check->idSubSection;
        } else {
            $subsection_data = [
                'idSection' => $section_id,
                'name' => $input,
                'alias' => $this->utils->safeLink($input)
            ];

            $subsection_id = (new Section)->addSubSection($subsection_data);
        }

        return $subsection_id;
    }

    private function getRemoteAuthors($input) {
        $src = [
            'BY ', ' AND'
        ];

        $rpl = [
            '', ','
        ];

        $input = str_replace($src, $rpl, $input);

        $input = ucwords(strtolower($input));

        $fullnames = explode(", ", $input);

        $found_name = explode(" ", $fullnames[0]);

        if(count($found_name) == 2) { // We have firstname and lastname
            return (object) [
                'firstname' => $found_name[0],
                'middlename' => null,
                'lastname' => $found_name[1]
            ];
        }

        /**
         * @todo Check how many word the presumed middle name has
         */
        if(count($found_name) > 2) { // We have firstname, middlename and lastname
            return (object) [
                'firstname' => $found_name[0],
                'middlename' => $found_name[1],
                'lastname' => $found_name[2]
            ];
        }
    }

    /**
     * Get the ID of an existing Author or, if it does not exist,
     * create a new Author and return his ID
     * @param $input
     * @return int|mixed
     */
    private function setAuthorId($input, $remote = false) {
        if($remote) {
            $input = $this->getRemoteAuthors($input);
        }

        $check = (new Author)->getByFullname([
            'firstname' => $input->firstname ?? null,
            'middlename' => $input->middlename ?? null,
            'lastname' => $input->lastname ?? null
        ]);

        if(!is_null($check)) {
            $author_id = $check->idAuthor;
        } else {
            $author_data = [
                'firstname' => $input->firstname ?? null,
                'middlename' => $input->middlename ?? null,
                'lastname' => $input->lastname ?? null,
                'alias' => $this->utils->safeLink($input->firstname.'-'.$input->middlename.'-'.$input->lastname)
            ];

            $author_id = (new Author)->addAuthor($author_data);
        }

        return $author_id;
    }

    private function setId($input): string
    {
        $src = [
            'nyt://article/',
            'nyt://video/',
            'nyt://interactive/'
        ];

        $rpl = [
            '',
            '',
            ''
        ];

        return trim(str_replace($src, $rpl, $input));
    }

    private function fetchRemoteImage($image_url) {
        $http_client = new Client([
            'timeout' => 30,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.5414.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
            ]
        ]);

        try {
            $response = $http_client->get($image_url);

            $response_code = $response->getStatusCode();

            if($response_code == '200') {
                if($response->hasHeader('Content-Type') && false !== strpos($response->getHeader('Content-Type')[0], "image")) {
                    $remote_image_body = $response->getBody();
                    $image_body = (string) $remote_image_body;

                    $remote_filename = $this->utils->getFileName($image_url, false);

                    if(file_put_contents($this->config->storage->news.$remote_filename, $image_body)) {
                        return $remote_filename;
                    }
                }
            }
        } catch(RequestException $exception) {
            /**
             * @todo Do something with this information later on (log it somewhere for later)
             */
            $url = $exception->getRequest()->getUri()->getScheme().'://'.$exception->getRequest()->getUri()->getHost().$exception->getRequest()->getUri()->getPath();

            $message = explode(" response", $exception->getMessage());

            $_SESSION['errors'][] = [
                'url' => $url,
                'error' => $message[0]
            ];
        } catch(ConnectException $exception) {
            /**
             * @todo Do something with this information later on (log it somewhere for later)
             */
            $_SESSION['errors'][] = [
                'url' => $exception->getHandlerContext()['url'],
                'error' => $exception->getHandlerContext()['error']
            ];
        }
    }

    private function findRemoteImage($url) {
        $http_client = new Client([
            'timeout' => 30,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.5414.119 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
            ]
        ]);

        try {
            $response = $http_client->get($url);

            $response_code = $response->getStatusCode();

            if($response_code == '200') {
                if($response->hasHeader('Content-Type') && false !== strpos($response->getHeader('Content-Type')[0], "text/html")) {
                    $remote_body = $response->getBody();
                    $body = (string) $remote_body;

                    if(preg_match("/<picture><source/", $body)) {
                        $pattern = "/\" src=\"https:\/\/(.*)-articleLarge\.jpg\?quality=75&amp;auto=webp&amp;disable=upscale\" srcSet=/isU";

                        preg_match($pattern, $body, $matches);

                        if(is_array($matches) && isset($matches[1]) && $matches[1] !== '') {
                            $image_url = 'https://'.$matches[1].'-articleLarge.jpg';

                            return $this->fetchRemoteImage($image_url);
                        }
                    }
                }
            }
        } catch(RequestException $exception) {
            /**
             * @todo Do something with this information later on (log it somewhere for later)
             */
            $url = $exception->getRequest()->getUri()->getScheme().'://'.$exception->getRequest()->getUri()->getHost().$exception->getRequest()->getUri()->getPath();

            $message = explode(" response", $exception->getMessage());

            $_SESSION['errors'][] = [
                'url' => $url,
                'error' => $message[0]
            ];
        } catch(ConnectException $exception) {
            /**
             * @todo Do something with this information later on (log it somewhere for later)
             */
            $_SESSION['errors'][] = [
                'url' => $exception->getHandlerContext()['url'],
                'error' => $exception->getHandlerContext()['error']
            ];
        }
    }

    private function importLocalContent(): void
    {
        $file = (new \system\Settings)->getSettingValue('content_file_select');

        $items = Items::fromFile($this->config->storage->files.$file);

        foreach($items as $item) {
            $section_id = isset($item->section_name) ? $this->setSectionId($item->section_name) : '0';
            $subsection_id = isset($item->subsection_name) ? $this->setSubSectionId($section_id, $item->subsection_name) : '0';
            $author_id = isset($item->byline->person[0]) ? $this->setAuthorId($item->byline->person[0]) : '0';
            $nyt_id = $this->setId($item->_id);

            $article_data = [
                'idSection' => $section_id,
                'idSubSection' => $subsection_id,
                'idAuthor' => $author_id,
                'id' => $nyt_id,
                'title' => $item->abstract,
                'url' => $item->web_url,
                'lead_paragraph' => $item->lead_paragraph,
                'source' => $item->source,
                'image' => $this->findRemoteImage($item->web_url),
                'published' => strtotime($item->pub_date),
                'created' => time()
            ];

            (new Article)->add($article_data);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function importRemoteContent() {
        $remote_data = file_get_contents((new \system\Settings)->getSettingValue('nyt_endpoint').'?api-key='.(new \system\Settings)->getSettingValue('nyt_apikey'));

        $temporary_json_file_name = $this->utils->randomString(6).'.json';

        file_put_contents($this->config->storage->temp.$temporary_json_file_name, $remote_data);

        // wait for the temp file to be written
        while(!file_exists($this->config->storage->temp.$temporary_json_file_name)) {
            sleep(1);
        }

        $items = Items::fromFile($this->config->storage->temp.$temporary_json_file_name, ['pointer' => '/results']);

        foreach($items as $item) {
            $section_id = isset($item->section) ? $this->setSectionId($item->section) : '0';
            $subsection_id = isset($item->subsection) ? $this->setSubSectionId($section_id, $item->subsection) : '0';
            $author_id = isset($item->byline) ? $this->setAuthorId($item->byline, true) : '0';
            $nyt_id = $this->setId($item->uri);

            $article_data = [
                'idSection' => $section_id,
                'idSubSection' => $subsection_id,
                'idAuthor' => $author_id,
                'id' => $nyt_id,
                'title' => $item->title,
                'url' => $item->url,
                'lead_paragraph' => $item->abstract,
                'source' => $item->source,
                'image' => isset($item->multimedia[2]->url) ? $this->fetchRemoteImage($item->multimedia[2]->url) : null,
                'published' => strtotime($item->published_date),
                'created' => time()
            ];

            (new Article)->add($article_data);
        }

        (new Upload)->cleanup($temporary_json_file_name);
    }
}