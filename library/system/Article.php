<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Article utilities
 */

namespace system;

use helpers\Utils;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class Article {
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

    public function add($data): void
    {
        DB::table('articles')->insertOrIgnore($data);
    }

    public function countAll(): int
    {
        return DB::table('articles')->count();
    }

    /**
     * @throws \JsonException
     */
    public function getAll($pagination_data = []): Collection
    {
        $query = DB::table('articles');

        $order_by_field = 'idArticle';
        $order_by_direction = 'desc';

        if(count($pagination_data) > 0) {
            $query->skip($pagination_data['offset'])
                ->take($pagination_data['limit']);
        }

        $query->orderBy($order_by_field, $order_by_direction);

        return $query->get();
    }

    public function getAllByDate($date_parts) {
        $from_time = strtotime($date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0].' 00:00:00');
        $to_time = strtotime($date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0].' 23:59:59');

        $order_by_field = 'idArticle';
        $order_by_direction = 'desc';

        return DB::table('articles')
            ->whereBetween('published', [$from_time, $to_time])
            ->orderBy($order_by_field, $order_by_direction)
            ->get();
    }

    public function countByAuthor($author_id): int
    {
        return DB::table('articles')
            ->where("idAuthor", $author_id)
            ->count();
    }

    /**
     * @throws \JsonException
     */
    public function getByAuthor($author_id, $pagination_data = []): Collection
    {
        $query = DB::table('articles')
            ->where("idAuthor", $author_id);

        $order_by_field = 'idArticle';
        $order_by_direction = 'desc';

        if(count($pagination_data) > 0) {
            $query->skip($pagination_data['offset'])
                ->take($pagination_data['limit']);
        }

        $query->orderBy($order_by_field, $order_by_direction);

        return $query->get();
    }

    public function countBySection($section_id): int
    {
        return DB::table('articles')
            ->where("idSection", $section_id)
            ->count();
    }

    /**
     * @throws \JsonException
     */
    public function getBySection($section_id, $pagination_data = []): Collection
    {
        $query = DB::table('articles')
            ->where("idSection", $section_id);

        $order_by_field = 'idArticle';
        $order_by_direction = 'desc';

        if(count($pagination_data) > 0) {
            $query->skip($pagination_data['offset'])
                ->take($pagination_data['limit']);
        }

        $query->orderBy($order_by_field, $order_by_direction);

        return $query->get();
    }

    public function countBySubSection($subsection_id): int
    {
        return DB::table('articles')
            ->where("idSubSection", $subsection_id)
            ->count();
    }

    public function get($id): object|null
    {
        return DB::table('articles')
            ->where("idArticle", $id)
            ->orWhere('id', 'LIKE', $id)
            ->first();
    }

    public function search($input): Collection
    {
        $order_by_field = 'idArticle';
        $order_by_direction = 'desc';

        return DB::table('articles')
            ->where("title", 'LIKE', '%'.$input.'%')
            ->orWhere('lead_paragraph', 'LIKE', '%'.$input.'%')
            ->orWhere('source', 'LIKE', '%'.$input.'%')
            ->orderBy($order_by_field, $order_by_direction)
            ->get();
    }

    public function getLatest(): object|null
    {
        return DB::table('articles')
            ->orderBy('idArticle', 'desc')
            ->first();
    }

    public function getLatestInSection($section_id): object|null
    {
        return DB::table('articles')
            ->where("idSection", $section_id)
            ->orderBy('idArticle', 'desc')
            ->first();
    }

    public function getLatestByAuthor($section_id): object|null
    {
        return DB::table('articles')
            ->where("idAuthor", $section_id)
            ->orderBy('idArticle', 'desc')
            ->first();
    }
}