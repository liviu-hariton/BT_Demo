<?php
/**
 * BT_Demo App
 * Author: Liviu Hariton <liviu.hariton@gmail.com>
 *
 * SDK library used to consume the BT_Demo API
 */

namespace BT_Demo;

class BT_Demo_API {
    /**
     * The host where the API responds
     */
    const _API_ENDPOINT = 'https://btdemo.liviuhariton.com/api/';

    /**
     * Sections endpoints
     */
    const _ARTICLES_ENDPOINT = 'articles';
    const _SECTIONS_ENDPOINT = 'sections';
    const _AUTHORS_ENDPOINT = 'authors';
    const _SETTINGS_ENDPOINT = 'settings';

    /**
     * Make an API request using HTTP client
     *
     * @param string $section Main API section
     * @param string $action The action to call in the main section
     * @param string $parameter The action parameter, if required by it
     */
    private function request(string $section, string $action, string $parameter = '') {
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json'
        );

        $url = self::_API_ENDPOINT.$section.'/'.$action;

        if($parameter !== '') {
            $url .= '/'.$parameter;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $result = curl_exec($ch);

        curl_close($ch);

        if($result === false) {
            $output = [
                'curl_error_number' => curl_errno($ch),
                'curl_error' => htmlspecialchars(curl_error($ch))
            ];

            rewind($verbose);

            $verbose_log = stream_get_contents($verbose);

            $output['curl_log'] = htmlspecialchars($verbose_log);

            echo json_encode($output);
        } else {
            curl_close($ch);

            return $result;
        }
    }

    /**
     * Return decoded JSON response as associative or empty array.
     *
     * @param string $response
     * @return mixed
     */
    public function decodeJsonResponse(string $response): mixed
    {
        if(!empty($response)) {
            return json_decode($response, true);
        }

        return json_decode('{}', true);
    }

    /**
     * Get all available articles
     * @param int $page_no Current page number
     */
    public function getAllArticles(int $page_no = 1): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getall', $page_no);
    }

    /**
     * Get the latest article (most recent)
     */
    public function getLatestArticle(): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getlatest');
    }

    /**
     * Get the latest article (most recent) from a specific section
     * @param int $section_id The Section's ID
     */
    public function getSectionLatestArticle(int $section_id): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getlatestinsection', $section_id);
    }

    /**
     * Get the latest article (most recent) published by a specific author
     * @param int $author_id The Author's ID
     */
    public function getAuthorLatestArticle(int $author_id): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getlatestbyauthor', $author_id);
    }

    /**
     * Get all available articles published on a specific date
     * @param string $date Date format: dd.mm.yyyy
     */
    public function getDateArticles(string $date): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getbydate', $date);
    }

    /**
     * Get a specific article
     * @param int|string $id The article's internal ID or the NYT ID
     */
    public function getArticle(int|string $id): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getbyid', $id);
    }

    /**
     * Search all articles that match a specific criteria
     * @param string $criteria The search criteria
     */
    public function searchArticles(string $criteria): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'search', $criteria);
    }

    /**
     * Get the articles from a specific section
     * @param int $section_id The Section's ID
     */
    public function getSectionArticles(int $section_id): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getbysection', $section_id);
    }

    /**
     * Get the articles published by a specific author
     * @param int $author_id The Author's ID
     */
    public function getAuthorArticles(int $author_id): bool|string|null
    {
        return $this->request(self::_ARTICLES_ENDPOINT, 'getbyauthor', $author_id);
    }

    /**
     * Get all available sections
     */
    public function getAllSections(): bool|string|null
    {
        return $this->request(self::_SECTIONS_ENDPOINT, 'getall');
    }

    /**
     * Get a specific section
     * @param int $id The section's internal ID
     */
    public function getSection(int $id): bool|string|null
    {
        return $this->request(self::_SECTIONS_ENDPOINT, 'getbyid', $id);
    }

    /**
     * Get a specific section by its slug
     * @param string $slug The section's slug
     */
    public function getSectionBySlug(string $slug): bool|string|null
    {
        return $this->request(self::_SECTIONS_ENDPOINT, 'getbyslug', $slug);
    }

    /**
     * Search all sections that match a specific criteria
     * @param string $criteria The search criteria
     */
    public function searchSections(string $criteria): bool|string|null
    {
        return $this->request(self::_SECTIONS_ENDPOINT, 'search', $criteria);
    }

    /**
     * Get all available authors
     */
    public function getAllAuthors(): bool|string|null
    {
        return $this->request(self::_AUTHORS_ENDPOINT, 'getall');
    }

    /**
     * Get a specific author
     * @param int $id The author's internal ID
     */
    public function getAuthor(int $id): bool|string|null
    {
        return $this->request(self::_AUTHORS_ENDPOINT, 'getbyid', $id);
    }

    /**
     * Get a specific author by its slug
     * @param string $slug The author's slug
     */
    public function getAuthorBySlug(string $slug): bool|string|null
    {
        return $this->request(self::_AUTHORS_ENDPOINT, 'getbyslug', $slug);
    }

    /**
     * Search all authors that match a specific criteria
     * @param string $criteria The search criteria
     */
    public function searchAuthors(string $criteria): bool|string|null
    {
        return $this->request(self::_AUTHORS_ENDPOINT, 'search', $criteria);
    }

    /**
     * Get the current value for a specific setting
     * @param string $setting The setting name
     */
    public function getSetting(string $setting): bool|string|null
    {
        return $this->request(self::_SETTINGS_ENDPOINT, 'get', $setting);
    }
}