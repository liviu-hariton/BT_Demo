<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Section / Subsection utilities
 */

namespace system;

use helpers\Utils;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class Section {
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

    public function getSectionByName($input): object|null
    {
        return DB::table('sections')
            ->where('name', 'like', $input)
            ->first();
    }

    public function addSection($data): int
    {
        return DB::table('sections')->insertGetId($data);
    }

    public function getSubSectionByName($section_id, $input): object|null
    {
        return DB::table('subsections')
            ->where('idSection', '=', $section_id)
            ->where('name', 'like', $input)
            ->first();
    }

    public function addSubSection($data): int
    {
        return DB::table('subsections')->insertGetId($data);
    }

    public function countAllSections(): int
    {
        return DB::table('sections')->count();
    }

    /**
     * @throws \JsonException
     */
    public function getAllSections($pagination_data = []): Collection
    {
        $query = DB::table('sections');

        $order_by_field = 'name';
        $order_by_direction = 'asc';

        if(count($pagination_data) > 0) {
            $query->skip($pagination_data['offset'])
                ->take($pagination_data['limit']);
        }

        $query->orderBy($order_by_field, $order_by_direction);

        return $query->get();
    }

    public function getSection($id): object|null
    {
        return DB::table('sections')
            ->where('idSection', '=', $id)
            ->first();
    }

    public function getSubSection($id): object|null
    {
        return DB::table('subsections')
            ->where('idSubSection', '=', $id)
            ->first();
    }

    public function countSubsections($section_id): int
    {
        return DB::table('subsections')
            ->where("idSection", $section_id)
            ->count();
    }

    /**
     * @throws \JsonException
     */
    public function getSubSections($section_id): Collection
    {
        return DB::table('subsections')
            ->where("idSection", $section_id)
            ->get();
    }

    public function search($input): Collection
    {
        return DB::table('sections')
            ->where("name", 'LIKE', '%'.$input.'%')
            ->get();
    }

    public function getSectionBySlug($slug): object|null
    {
        return DB::table('sections')
            ->where('alias', 'like', $slug)
            ->first();
    }
}