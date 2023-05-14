<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Various utilities
 */

namespace helpers;

use DateTime;
use DateTimeZone;
use IntlDateFormatter;

class Utils {
    private mixed $config;

    public function __construct() {
        global $config;

        $this->config = $config;
    }

    public function printr($input): void
    {
        echo '<pre class="pre-debug">';
        print_r($input);
        echo '</pre>';
    }

    public function redirect($location, $params = [], $permanent = false) {
        $destination = _URL.$location;

        if(count($params) > 0) {
            $destination .= '?'.http_build_query($params);
        }

        if($permanent === true) {
            header("HTTP/1.1 301 Moved Permanently");
        }

        header("location: ".$destination);
        exit;
    }

    public function flatArray(array $array): array
    {
        $return = [];

        array_walk_recursive($array, static function($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    public function randomPassword($length): string
    {
        $alphanum = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $special  = '~!@#$%^&*(){}[],./?';

        $pool = $alphanum.$special;

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function randomString($length): string
    {
        $alphanum = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        return substr(str_shuffle(str_repeat($alphanum, $length)), 0, $length);
    }

    public function randomNumber($length): string
    {
        $alphanum = '0123456789';

        return substr(str_shuffle(str_repeat($alphanum, $length)), 0, $length);
    }

    public function getIP(): string
    {
        $ipaddress = '0.0.0.0';

        if(getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if(getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if(getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if(getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if(getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if(getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        }

        return $ipaddress;
    }

    public function deleteFile($path, $file): void
    {
        if(file_exists($path.$file)) {
            unlink($path.$file);
        }
    }

    public function chunkFile($filepath, $lines = 1, $adaptive = true): string
    {
        $f = @fopen($filepath, "rb");

        if($f === false) {
            return false;
        }

        $buffer = !$adaptive ? 4096 : ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        fseek($f, -1, SEEK_END);

        if(fread($f, 1) != "\n") {
            $lines -= 1;
        }

        $output = '';
        $chunk = '';

        while(ftell($f) > 0 && $lines >= 0) {
            $seek = min(ftell($f), $buffer);

            fseek($f, -$seek, SEEK_CUR);

            $output = ($chunk = fread($f, $seek)) . $output;

            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            $lines -= substr_count($chunk, "\n");
        }

        while ($lines++ < 0) {
            $output = substr($output, strpos($output, "\n") + 1);
        }

        fclose($f);

        return trim($output);
    }

    public function getFileSize($path): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

        if(is_file($path)) {
            $file_bytes = filesize($path);

            for($i = 0; $file_bytes >= 1024 && $i < (count($units) - 1); $file_bytes /= 1024, $i++);

            return(round($file_bytes, 2)." ".$units[$i]);

        }

        return false;
    }

    public function getFileName($input, $strip_extension = true): string
    {
        $file_name = '';

        if($input != '') {
            $parts = pathinfo($input);

            $file_name = $parts['filename'];

            if($strip_extension === false) {
                $file_name .= '.'.$parts['extension'];
            }
        }

        return $file_name;
    }

    public function getFileExtension($input): string
    {
        $extension = '';

        if($input != '') {
            $parts = pathinfo($input);

            $extension = $parts['extension'];
        }

        return $extension;
    }

    public function getFileTime($input, $format = 'd/m/Y H:i:s'): string
    {
        $utime = filectime($input);

        return date($format, $utime);
    }

    public function safeFileName($input, $remove = array(), $delimiter = '-', $maxLength = 100): string
    {
        if(!empty($remove)) {
            $str = str_replace((array)$remove, '', $input);
        }

        $src = array('&amp;');
        $rpl = array('');

        $input = str_replace($src, $rpl, $input);

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $clean = preg_replace("/[^a-zA-Z0-9\/_\.|+ -]/", '', $clean);
        $clean = strtolower(trim(substr($clean, 0, $maxLength), '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function renameFile($path, $old_name, $new_name): void
    {
        if(is_file($path.$old_name)) {
            rename($path.$old_name, $path.$new_name);
        }
    }

    public function deleteFolder($path): void
    {
        if(is_dir($path)) {
            rmdir($path);
        }
    }

    public function getCurrentURI(): string
    {
        return isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 1) : '';
    }

    /**
     * @throws \Exception
     */
    public function dateLiteral($params = []): string
    {
        $date_object = new DateTime(date('Y-m-d H:i:s', $params['timestamp']), new DateTimeZone(_TIME_ZONE));

        $date_format = match($params['date_type']) {
            'NONE' => IntlDateFormatter::NONE,
            'SHORT' => IntlDateFormatter::SHORT,
            'LONG' => IntlDateFormatter::LONG,
            'FULL' => IntlDateFormatter::FULL,
            default => IntlDateFormatter::MEDIUM
        };

        $time_format = match($params['time_type']) {
            'NONE' => IntlDateFormatter::NONE,
            'SHORT' => IntlDateFormatter::SHORT,
            'LONG' => IntlDateFormatter::LONG,
            'FULL' => IntlDateFormatter::FULL,
            default => IntlDateFormatter::MEDIUM
        };

        $local_date = IntlDateFormatter::create(
            _LOCALE_SHORT,
            $date_format,
            $time_format,
            _TIME_ZONE,
            null,
            $params['date_format']
        );

        return ucwords($local_date->format($date_object));
    }

    public function arrayToHTMLList($data): string
    {
        $out = '<dl class="row">';

        foreach($data as $k=>$v){
            if(is_array($v)) {
                $out .= '<dt class="col-sm-3 bg-secondary-100 pt-2"></dt><dd class="col-sm-9 pt-2">'.self::arrayToHTMLList($v).'</dd>';
            } else {
                $out .= '<dt class="col-sm-3 bg-secondary-100 pt-2">'.$k.'</dt>'.'<dd class="col-sm-9 pt-2">'.$v.'</dd>';
            }
        }

        $out .= '</dl>';

        return $out;
    }

    public function getInitials($input, $separator = ''): string
    {
        if($input != '') {
            $initials = [];

            $parts = explode(" ", $input);

            foreach($parts as $part) {
                $initials[] = strtoupper(mb_substr(trim($part), 0, 1));
            }

            return implode($separator, $initials);
        } else {
            return 'SYN';
        }
    }

    public function truncate($text, $numb, $etc = "...") {
        if(strlen($text) > $numb) {
            $text = substr($text, 0, $numb);

            $punctuation = ""; //punctuation characters to remove

            $text = (strspn(strrev($text), $punctuation) != 0) ? substr($text, 0, -strspn(strrev($text), $punctuation)) : $text;

            $text = $text.$etc;
        }

        return $text;
    }

    public function searchInObject($search_in, $search_for, $search_holder) {
        $result = null;

        foreach($search_in as $obj) {
            if($search_for == $obj->$search_holder) {
                $result = $obj;
                break;
            }
        }

        return $result;
    }

    public function consecutiveNumbers($min, $max, $step = 1): array
    {
        $numbers = [];

        foreach(range($min, $max, $step) as $number) {
            $numbers[] = $number;
        }

        return $numbers;
    }

    public function weekDays(): array
    {
        return [
            '1' => 'Luni',
            '2' => 'Marți',
            '3' => 'Miercuri',
            '4' => 'Joi',
            '5' => 'Vineri',
            '6' => 'Sâmbătă',
            '7' => 'Duminică'
        ];
    }

    public function dayHours(): array
    {
        return [
            '1' => '00:00',
            '2' => '00:30',
            '3' => '01:00',
            '4' => '01:30',
            '5' => '02:00',
            '6' => '02:30',
            '7' => '03:00',
            '8' => '03:30',
            '9' => '04:00',
            '10' => '04:30',
            '11' => '05:00',
            '12' => '05:30',
            '13' => '06:00',
            '14' => '06:30',
            '15' => '07:00',
            '16' => '07:30',
            '17' => '08:00',
            '18' => '08:30',
            '19' => '09:00',
            '20' => '09:30',
            '21' => '10:00',
            '22' => '10:30',
            '23' => '11:00',
            '24' => '11:30',
            '25' => '12:00',
            '26' => '12:30',
            '27' => '13:00',
            '28' => '13:30',
            '29' => '14:00',
            '30' => '14:30',
            '31' => '15:00',
            '32' => '15:30',
            '33' => '16:00',
            '34' => '16:30',
            '35' => '17:00',
            '36' => '17:30',
            '37' => '18:00',
            '38' => '18:30',
            '39' => '19:00',
            '40' => '19:30',
            '41' => '20:00',
            '42' => '20:30',
            '43' => '21:00',
            '44' => '21:30',
            '45' => '22:00',
            '46' => '22:30',
            '47' => '23:00',
            '48' => '23:30'
        ];
    }

    public function decrementLetter($letter) {
        $len = strlen($letter);

        if($len == 1) {
            if(strcasecmp($letter,"A") == 0) {
                return "A";
            }

            return chr(ord($letter) - 1);
        } else {
            $s = substr($letter, -1);

            if(strcasecmp($s, "A") == 0) {
                $s = substr($letter, -2, 1);

                if(strcasecmp($s, "A") == 0) {
                    $s = "Z";
                } else {
                    $s = chr(ord($s) - 1);
                }

                $output = substr($letter, 0, $len - 2) . $s;

                if(strlen($output) != $len && $letter != "AA") {
                    $output .= "Z";
                }

                return $output;
            } else {
                return substr($letter, 0, $len - 1) . chr(ord($s) - 1);
            }
        }
    }

    public function safeLink($input): string
    {
        $strip_words = explode(",", _SLUG_STRIP_WORDS);

        if(count($strip_words) > 0) {
            $input = preg_replace('/\b('.implode("|", $strip_words).')\b/iu', '', $input);
        }

        $input = trim($input);

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim(substr($clean, 0, _SLUG_MAX_LENGTH), '-'));
        $clean = preg_replace("/[\/_|+ -]+/", _SLUG_DELIMITER, $clean);

        return $clean;
    }
}