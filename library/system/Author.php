<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * Author utilities
 */

namespace system;

use helpers\Utils;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class Author {
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

    public function getByFullname($data): object|null
    {
        $query = DB::table('authors');

        if(!is_null($data['firstname'])) {
            $query->where('firstname', 'like', $data['firstname']);
        }

        if(!is_null($data['middlename'])) {
            $query->where('middlename', 'like', $data['middlename']);
        }

        if(!is_null($data['lastname'])) {
            $query->where('lastname', 'like', $data['lastname']);
        }

        return $query->first();
    }

    public function addAuthor($data): int
    {
        return DB::table('authors')->insertGetId($data);
    }

    public function countAll(): int
    {
        return DB::table('authors')->count();
    }

    /**
     * @throws \JsonException
     */
    public function getAll($pagination_data = []): Collection
    {
        $query = DB::table('authors');

        $order_by_field = 'idAuthor';
        $order_by_direction = 'desc';

        if(count($pagination_data) > 0) {
            $query->skip($pagination_data['offset'])
                ->take($pagination_data['limit']);
        }

        $query->orderBy($order_by_field, $order_by_direction);

        return $query->get();
    }

    public function getAuthor($id): object|null
    {
        return DB::table('authors')
            ->where('idAuthor', '=', $id)
            ->first();
    }

    public function search($input): Collection
    {
        return DB::table('authors')
            ->where("firstname", 'LIKE', '%'.$input.'%')
            ->orWhere("middlename", 'LIKE', '%'.$input.'%')
            ->orWhere("lastname", 'LIKE', '%'.$input.'%')
            ->get();
    }

    public function getAuthorBySlug($slug): object|null
    {
        return DB::table('authors')
            ->where('alias', 'like', $slug)
            ->first();
    }
}