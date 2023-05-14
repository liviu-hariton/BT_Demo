<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Files upload utilities
 */

namespace helpers;

class Upload {
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;
        $this->utils = new Utils;
    }

    public function file($params) {
        $filename = $this->utils->getFileName($_FILES[$params['input']]['name']);
        $extension = $this->utils->getFileExtension($_FILES[$params['input']]['name']);

        $file = $this->utils->safeFileName($filename).'-'.$this->utils->randomString(8).'.'.$extension;

        if(move_uploaded_file($_FILES[$params['input']]['tmp_name'], $params['path'].$file)) {
            return $file;
        } else {
            return false;
        }
    }

    public function cleanup($file): void
    {
        if(file_exists($this->config->storage->temp.$file)) {
            (new Utils)->deleteFile($this->config->storage->temp, $file);
        }
    }
}