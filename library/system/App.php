<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Overall app utilities
 */

namespace system;

use helpers\Utils;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class App {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;

        $this->utils = new Utils;
    }

    /**
     * Make a list with all available front-end templates
     * by reading the folder names in the containing folder
     * @return bool|array
     */
    public function getFrontTemplates(): bool|array
    {
        $data = [];

        $tpls = glob(_ROOT.'layout/front/*', GLOB_ONLYDIR);

        foreach($tpls as $tpl) {
            $data[] = str_replace([_ROOT, 'layout/front/'], ['', ''], $tpl);
        }

        return $data;
    }

    /**
     * Make a list with all available admin templates
     * by reading the folder names in the containing folder
     * @return bool|array
     */
    public function getAdminTemplates(): bool|array
    {
        $data = [];

        $tpls = glob(_ROOT.'layout/admin/*', GLOB_ONLYDIR);

        foreach($tpls as $tpl) {
            $data[] = str_replace([_ROOT, 'layout/admin/'], ['', ''], $tpl);
        }

        return $data;
    }

    /**
     * Predefine the available forms
     * @return array[]
     */
    private function adminForms(): array
    {
        return [
            // Save overall settings
            '1' => [
                'object' => '\app\admin\Settings',
                'method' => 'saveOverallSettings'
            ],
            // Save overall settings
            '2' => [
                'object' => '\app\admin\Settings',
                'method' => 'saveContentSettings'
            ]
        ];
    }

    /**
     * Process the provided form
     * @return void
     */
    public function postAdminRequest(): void
    {
        $forms = $this->adminForms();

        // only if it is predefined
        if(is_array($forms[$_POST['form']])) {
            $object = $forms[$_POST['form']]['object'];
            $method = $forms[$_POST['form']]['method'];

            unset($_POST['form'], $_POST['save_form_data']);

            call_user_func([
                new $object, $method
            ]);
        } else {
            $this->utils->redirect('admin/');
        }
    }

    /**
     * Make a list with all previously uploaded content files
     * by reading the files names and properties in the containing folder
     * @return bool|array
     */
    public function getContentFiles(): bool|array
    {
        $data = [];

        $files = glob($this->config->storage->files.'*');

        foreach($files as $file) {
            $data[] = [
                'file' => str_replace([$this->config->storage->files], [''], $file),
                'size' => $this->utils->getFileSize($file),
                'created' => $this->utils->getFileTime($file)
            ];
        }

        return $data;
    }
}