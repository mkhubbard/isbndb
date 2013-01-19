<?php

namespace IsbnDb;

use \IsbnDb\Exception\InvalidResponseException;

class BookResponse extends \IsbnDb\Response implements \Countable
{
    const COLLECTION_ROOT = "BookList";
    const COLLECTION_DATA = "BookData";

    protected $collection;

    public function clear()
    {
        parent::clear();

        $this->collection = array();
    }

    public function count()
    {
        return count($this->collection);
    }

    protected function parseCollection()
    {
        $xpath = new \DOMXPath($this->response);

        $collectionRoot = $xpath->query('/ISBNdb/BookList');
        $collectionData = $xpath->query('/ISBNdb/BookList/BookData');

        if ( $collectionRoot->length <= 0 ) {
            throw new InvalidResponseException('Result does not contain the expected collection data root.');
        }

        $this->parseCollectionPaging($collectionRoot->item(0));

        foreach($collectionData as $dataNode) {
            $this->collection[] = new Book($dataNode);
        }

    }
}
