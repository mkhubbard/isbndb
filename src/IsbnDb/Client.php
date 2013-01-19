<?php

namespace IsbnDb;

use IsbnDb\Exception\IsbnDbException;

class Client
{
    const DEFAULT_BASE_URL = "http://isbndb.org/api/";

    const COLLECTION_AUTHORS    = 0;
    const COLLECTION_BOOK       = 1;
    const COLLECTION_CATEGORIES = 2;
    const COLLECTION_PUBLISHERS = 3;
    const COLLECTION_SUBJECTS   = 4;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    private $endpoints;

    public function __construct($apiKey)
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('Empty API key passed to constructor.');
        }

        $this->apiKey = $apiKey;

        $this->endpoints = array(
            self::COLLECTION_AUTHORS    => 'authors.xml',
            self::COLLECTION_BOOK       => 'books.xml',
            self::COLLECTION_CATEGORIES => 'categories.xml',
            self::COLLECTION_PUBLISHERS => 'publishers.xml',
            self::COLLECTION_SUBJECTS   => 'subjects.xml'
        );
    }

    public function query($catalog, $index, $value, $opts = array())
    {
        if (empty($index) || empty($value)) {
            throw new \InvalidArgumentException('Query request requires "index" and "value" parameters to be specified.');
        }

        $endpoint = $this->getCatalogEndpoint($catalog);
        if ($endpoint === false) {
            throw new \InvalidArgumentException('Invalid catalog requested for query operation.');
        }

        // Temporary checks for catalogs that have not been implemented yet.
        if ($endpoint !== $this->endpoints[self::COLLECTION_BOOK]) {
            throw new IsbnDbException('The requested catalog is not currently supported by this library.');
        }

        $url = $this->getRequestUrl($endpoint, $index, $value, $opts);

        $raw = $this->execute($url);

        $response = false;

        if ($raw !== false) {
            switch($catalog) {
                case self::COLLECTION_BOOK:
                    $response = new BookResponse($raw);
            }
        }

        return $response;
    }

    /**
     * @param $catalog
     * @return bool
     */
    private function getCatalogEndpoint($catalog)
    {
        $result = false;

        if (isset($this->endpoints[$catalog])) {
            $result = $this->endpoints[$catalog];
        }

        return $result;
    }

    private function getRequestUrl($endpoint, $index1, $value1, $opts = array())
    {
        $params = array(
            'access_key' => $this->apiKey,
            'index1'     => $index1,
            'value1'     => $value1
        );

        if (!empty($opts['results'])) {
            $params['results'] = $opts['results'];
        }

        $query = http_build_query($params);

        return self::DEFAULT_BASE_URL . $endpoint . '?' . $query;
    }

    /**
     * @param $url
     * @return mixed
     * @throws Exception\RequestException
     */
    private function execute($url)
    {
        $curl = curl_init($url);
        if ($curl === false) {
            throw new \IsbnDb\Exception\RequestException('Failed to initialize cURL handle.');
        }

        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        if ($status !== 200) {
            throw new \IsbnDb\Exception\RequestException('Client request failed: ' . $error);
        }

        return $response;
    }
}
