<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Template engine
 */

namespace helpers;

class View {
    var array $_tpldata = [];
    var array $files = [];
    var string $root = "";
    var array $compiled_code = [];
    var array $uncompiled_code = [];

    function __construct($root = ".") {
        $this->set_rootdir($root);
    }

    function destroy(): void
    {
        $this->_tpldata = [];
    }

    function set_rootdir($dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $this->root = $dir;

        return true;
    }

    function set_filenames($filename_array): bool
    {
        if (!is_array($filename_array)) {
            return false;
        }

        reset($filename_array);

        foreach($filename_array as $handle=>$filename) {
            $this->files[$handle] = $this->make_filename($filename);
        }

        return true;
    }

    function pparse($handle, $return = false) {
        if (!$this->loadfile($handle)) {
            die("Template->pparse(): Couldn't load template file for handle $handle");
        }

        if (!isset($this->compiled_code[$handle]) || empty($this->compiled_code[$handle])) {
            $this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
        }

        if($return === false) {
            eval($this->compiled_code[$handle]);
            return true;
        } else {
            ob_start();

            eval($this->compiled_code[$handle]);

            return ob_get_clean();
        }
    }

    function assign_var_from_handle($varname, $handle) {
        if (!$this->loadfile($handle)) {
            die("Template->assign_var_from_handle(): Couldn't load template file for handle $handle");
        }

        $_str = "";
        $code = $this->compile($this->uncompiled_code[$handle], true, '_str');

        eval($code);
        $this->assign_var($varname, $_str);

        return true;
    }

    function assign_block_vars($blockname, $vararray): bool
    {
        if (str_contains($blockname, '.')) {
            $blocks = explode('.', $blockname);
            $blockcount = count($blocks) - 1;

            $str = '$this->_tpldata';

            for ($i = 0; $i < $blockcount; $i++) {
                $str .= '[\'' . $blocks[$i] . '.\']';
                eval('$lastiteration = @count(' . $str . ') - 1;');
                $str .= '[' . $lastiteration . ']';
            }
            $str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';

            eval($str);
        } else {
            $this->_tpldata[$blockname . '.'][] = $vararray;
        }

        return true;
    }

    function add_block_vars($blockname, $vararray): void
    {
        foreach($vararray as $vararray_key=>$vararray_value) {
            $this->_tpldata[$blockname . '.'][0][$vararray_key] = $vararray_value;
        }
    }

    function assign_vars($vararray): bool
    {
        reset($vararray);

        foreach($vararray as $key=>$val) {
            $this->_tpldata['.'][0][$key] = $val;
        }

        return true;
    }

    function assign_var($varname, $varval): bool
    {
        $this->_tpldata['.'][0][$varname] = $varval;

        return true;
    }

    function make_filename($filename) {
        if (!file_exists($this->root.$filename)) {
            die("Template->make_filename(): Error - file ".$this->root.$filename." does not exist");
        }

        return $filename;
    }

    function loadfile($handle) {
        if (isset($this->uncompiled_code[$handle]) && !empty($this->uncompiled_code[$handle])) {
            return true;
        }

        if (!isset($this->files[$handle])) {
            die("Template->loadfile(): No file specified for handle $handle");
        }

        $filename = $this->files[$handle];

        $str = implode("", @file($this->root.$filename));

        if(empty($str)) {
            die("Template->loadfile(): File ".$this->root.$filename." for handle \"$handle\" is empty");
        }

        $this->uncompiled_code[$handle] = $str;

        return true;
    }

    function compile($code, $do_not_echo = false, $retvar = ''): string
    {
        $code = str_replace(['\\', '\''], ['\\\\', '\\\''], $code);

        $varrefs = array();

        preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);

        $varcount = count($varrefs[1]);

        for ($i = 0; $i < $varcount; $i++) {
            $namespace = $varrefs[1][$i];
            $varname = $varrefs[3][$i];

            $new = $this->generate_block_varref($namespace, $varname);

            $code = str_replace($varrefs[0][$i], $new, $code);
        }

        $code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->_tpldata[\'.\'][0][\'\1\']) ) ? $this->_tpldata[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);

        $code_lines = explode("\n", $code);

        $block_nesting_level = 0;
        $block_names = array();
        $block_names[0] = ".";

        $line_count = count($code_lines);

        for($i = 0; $i < $line_count; $i++) {
            $code_lines[$i] = chop($code_lines[$i]);
            if(preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m)) {
                $n[0] = $m[0];
                $n[1] = $m[1];

                if(preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n) ) {
                    $block_nesting_level++;
                    $block_names[$block_nesting_level] = $m[1];

                    if($block_nesting_level < 2) {
                        $code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->_tpldata[\'' . $n[1] . '.\']) ) ?  sizeof($this->_tpldata[\'' . $n[1] . '.\']) : 0;';
                        $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
                        $code_lines[$i] .= "\n" . '{';
                    } else {
                        $namespace = implode('.', $block_names);
                        $namespace = substr($namespace, 2);

                        $varref = $this->generate_block_data_ref($namespace, false);

                        $code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
                        $code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
                        $code_lines[$i] .= "\n" . '{';
                    }

                    unset($block_names[$block_nesting_level]);

                    $block_nesting_level--;
                    $code_lines[$i] .= '} // END ' . $n[1];
                    $m[0] = $n[0];
                    $m[1] = $n[1];
                } else {
                    $block_nesting_level++;
                    $block_names[$block_nesting_level] = $m[1];

                    if($block_nesting_level < 2) {
                        $code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->_tpldata[\'' . $m[1] . '.\']) ) ? sizeof($this->_tpldata[\'' . $m[1] . '.\']) : 0;';
                        $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
                        $code_lines[$i] .= "\n" . '{';
                    } else {
                        $namespace = implode('.', $block_names);
                        $namespace = substr($namespace, 2);

                        $varref = $this->generate_block_data_ref($namespace, false);

                        $code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? sizeof(' . $varref . ') : 0;';
                        $code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
                        $code_lines[$i] .= "\n" . '{';
                    }
                }
            } else if(preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m)) {
                unset($block_names[$block_nesting_level]);

                $block_nesting_level--;
                $code_lines[$i] = '} // END ' . $m[1];
            } else {
                if(!$do_not_echo) {
                    $code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
                } else {
                    $code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";';
                }
            }
        }

        $code = implode("\n", $code_lines);

        return $code;
    }

    function generate_block_varref($namespace, $varname): string
    {
        $namespace = substr($namespace, 0, strlen($namespace) - 1);

        $varref = $this->generate_block_data_ref($namespace, true);
        $varref .= '[\'' . $varname . '\']';
        $varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';

        return $varref;
    }

    function generate_block_data_ref($blockname, $include_last_iterator): string
    {
        $blocks = explode(".", $blockname);
        $blockcount = count($blocks) - 1;
        $varref = '$this->_tpldata';

        for ($i = 0; $i < $blockcount; $i++) {
            $varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
        }

        $varref .= '[\'' . $blocks[$blockcount] . '.\']';

        if ($include_last_iterator) {
            $varref .= '[$_' . $blocks[$blockcount] . '_i]';
        }

        return $varref;
    }
}