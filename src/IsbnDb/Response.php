<?php

namespace IsbnDb;

use \IsbnDb\Exception\InvalidResponseException;

abstract class Response {

    /** @var Boolean */
    protected $valid;

    /** @var Integer */
    protected $resultPageSize;

    /** @var Integer */
    protected $resultPage;

    /** @var Integer */
    protected $resultCount;

    /** @var Integer */
    protected $resultTotal;

    /** @var \DateTime */
    protected $serverTime;

    /** @var \DOMDocument */
    protected $response;

    public static function stringToDateTime($raw)
    {
        try {
            $result = new \DateTime($raw);
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    public function __construct($responseXml)
    {
        assert(is_string($responseXml));

        $this->serverTime = new \DateTime();

        $this->clear();

        $this->parse($responseXml);
    }

    public function clear()
    {
        $this->valid = false;
        $this->resultPageSize = 0;
        $this->resultPage = 0;
        $this->resultCount = 0;
        $this->resultTotal = 0;
        $this->serverTime->setTimestamp(0);
    }

    public function getPageSize()
    {
        return $this->resultPageSize;
    }

    public function getCurrentPage()
    {
        return $this->resultPage;
    }

    public function getResultCount()
    {
        return $this->resultCount;
    }

    public function getResultTotal()
    {
        return $this->resultTotal;
    }

    abstract protected function parseCollection();

    protected function parseCollectionPaging(\DOMElement $element)
    {
        $valid = $element->hasAttribute('total_results');
        $valid = $valid && $element->hasAttribute('page_size');
        $valid = $valid && $element->hasAttribute('page_number');
        $valid = $valid && $element->hasAttribute('shown_results');

        if (!$valid) {
            throw new InvalidResponseException('Result paging information is not present.');
        }

        $this->resultTotal = $element->attributes->getNamedItem('total_results')->nodeValue;
        $this->resultPageSize = $element->attributes->getNamedItem('page_size')->nodeValue;
        $this->resultPage = $element->attributes->getNamedItem('page_number')->nodeValue;
        $this->resultCount = $element->attributes->getNamedItem('shown_results')->nodeValue;

    }

    private function parse($responseXml)
    {
        $this->response = new \DOMDocument("", "UTF-8");
        $this->response->loadXML($responseXml);
        $rootNode = $this->response->firstChild;

        if (strtolower($rootNode->nodeName) !== 'isbndb') {
            throw new Exception\InvalidResponseException('Malformed resonse received from server.');
        }

        if ( $rootNode->hasAttribute('server_time') ) {
            $checkTime = strtotime($rootNode->attributes->getNamedItem('server_time')->nodeValue);

            // Treat failure to parse server time as a non-fatal error; set serverTime property to current
            // time of our server so there is at least some form of a sane value.
            if ($checkTime === FALSE) {
                $checkTime = now();
            }

            $this->serverTime->setTimestamp($checkTime);
        }

        $this->parseCollection();
    }

}