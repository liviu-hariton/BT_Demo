<?php

namespace Westsworld\TimeAgo\Translations;

use \Westsworld\TimeAgo\Language;

/**
 * English translations
 */
class Ro extends Language
{
    public function __construct()
    {
        $this->setTranslations([
            'aboutOneDay' => "acum 1 zi",
            'aboutOneHour' => "acum 1 oră",
            'aboutOneMonth' => "acum 1 lună",
            'aboutOneYear' => "acum 1 an",
            'days' => "acum %s zile",
            'hours' => "acum %s ore",
            'lessThanAMinute' => "acum",
            'lessThanOneHour' => "acum %s minute",
            'months' => "acum %s luni",
            'oneMinute' => "acum 1 minut",
            'years' => "acum %s ani",
            'never' => 'niciodată'
        ]);
    }
}