<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Settings utilities
 */

namespace system;

use helpers\Utils;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class Settings
{
    /**
     * @var array|mixed
     */
    private $config;
    private Utils $utils;

    public function __construct() {
        global $config;

        $this->config = $config;

        $this->utils = new Utils;
    }

    public function saveOverallSettings($data): void
    {
        foreach($data as $key=>$value) {
            DB::table('settings')
                ->where('name', '=', $key)
                ->update([
                    'value' => $value
                ]);
        }
    }

    public function saveRemoteContentSettings($data): void
    {
        foreach($data as $key=>$value) {
            DB::table('settings')
                ->where('name', '=', $key)
                ->update([
                    'value' => $value
                ]);
        }
    }

    public function saveLocalContentSettings($data): void
    {
        foreach($data as $key=>$value) {
            DB::table('settings')
                ->where('name', '=', $key)
                ->update([
                    'value' => $value
                ]);
        }
    }

    public function getSettingValue($field): string|null
    {
        $data = DB::table('settings')
            ->where('name', '=', $field)
            ->first();

        return $data?->value;
    }
}