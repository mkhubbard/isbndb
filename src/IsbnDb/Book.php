<?php

namespace IsbnDb;

class Book {

    /**
     * @var string|null
     */
    protected $bookId;

    /**
     * @var string
     */
    protected $isbn;

    /**
     * @var string
     */
    protected $isbn13;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $titleLong;

    /**
     * @var string
     */
    protected $authorsText;

    /**
     * @var string
     */
    protected $publisherId;

    /**
     * @var string
     */
    protected $publisherText;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var string
     */
    protected $urlsText;

    /**
     * @var string
     */
    protected $awardsText;

    /**
     * @var \DateTime|boolean
     */
    protected $changeTime;

    /**
     * @var \DateTime|boolean
     */
    protected $priceTime;

    /**
     * @var string
     */
    protected $editionInfo;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $physicalDescText;

    /**
     * @var string
     */
    protected $lccNumber;

    /**
     * @var string
     */
    protected $dewyDecimalNormalized;

    /**
     * @var string
     */
    protected $dewyDecimal;


    public function __construct(\DOMElement $node)
    {
        $this->clear();
        $this->parse($node);
    }

    public function clear()
    {
        $this->bookId = null;
        $this->isbn = '';
        $this->isbn13 = '';
        $this->title = '';
        $this->titleLong = '';
        $this->authorsText = '';
        $this->publisherId = null;
        $this->publisherText = '';
        $this->summary = '';
        $this->notes = '';
        $this->urlsText = '';
        $this->awardsText = '';
        $this->changeTime = false;
        $this->priceTime = false;
        $this->editionInfo = '';
        $this->language = '';
        $this->physicalDescText = '';
        $this->lccNumber = '';
        $this->dewyDecimalNormalized = '';
        $this->dewyDecimal = '';
    }

    /**
     * @return string
     */
    public function getAuthorsText()
    {
        return $this->authorsText;
    }

    /**
     * @return string
     */
    public function getAwardsText()
    {
        return $this->awardsText;
    }

    /**
     * @return null|string
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * @return \DateTime|boolean
     */
    public function getChangeTime()
    {
        return $this->changeTime;
    }

    /**
     * @return string
     */
    public function getDewyDecimal()
    {
        return $this->dewyDecimal;
    }

    /**
     * @return string
     */
    public function getDewyDecimalNormalized()
    {
        return $this->dewyDecimalNormalized;
    }

    /**
     * @return string
     */
    public function getEditionInfo()
    {
        return $this->editionInfo;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getIsbn13()
    {
        return $this->isbn13;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getLccNumber()
    {
        return $this->lccNumber;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getPhysicalDescText()
    {
        return $this->physicalDescText;
    }

    /**
     * @return \DateTime|boolean
     */
    public function getPriceTime()
    {
        return $this->priceTime;
    }

    /**
     * @return string
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * @return string
     */
    public function getPublisherText()
    {
        return $this->publisherText;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getTitleLong()
    {
        return $this->titleLong;
    }

    /**
     * @return string
     */
    public function getUrlsText()
    {
        return $this->urlsText;
    }

    private function parse(\DOMElement $node)
    {
        foreach($node->childNodes as $child) {
            if (!is_a($child, 'DOMElement')) {
                continue;
            }

            switch(strtolower($child->tagName)) {
                case 'bookdata':
                    $this->parseBookDataNode($child);
                    break;

                case 'title':
                    $this->title = $child->nodeValue;
                    break;

                case 'titlelong':
                    $this->titleLong = $child->nodeValue;
                    break;

                case 'authorstext':
                    $this->authorsText = $child->nodeValue;
                    break;

                case 'publishertext':
                    if ( $child->hasAttribute('publisher_id') ) {
                        $this->publisherId = $child->attributes->getNamedItem('publisher_id')->nodeValue;
                    }
                    $this->publisherText = $child->nodeValue;
                    break;

                case 'details':
                    $this->parseDetailNode($child);
                    break;

                case 'summary':
                    $this->summary = $child->nodeValue;
                    break;

                case 'notes':
                    $this->notes = $child->nodeValue;
                    break;

                case 'urlstext':
                    $this->urlsText = $child->nodeValue;
                    break;

                case 'awardstext':
                    $this->awardsText = $child->nodeValue;
                    break;

                default:
                    // unhandled values are OK
            }
        }
    }

    private function parseBookDataNode(\DOMElement $node)
    {
        if ( $node->hasAttribute('book_id') ) {
            $this->bookId = $node->attributes->getNamedItem('book_id')->nodeValue;
        }

        if ( $node->hasAttribute('isbn') ) {
            $this->isbn = $node->attributes->getNamedItem('isbn')->nodeValue;
        }

        if ( $node->hasAttribute('isbn13') ) {
            $this->isbn13 = $node->attributes->getNamedItem('isbn13')->nodeValue;
        }
    }

    private function parseDetailNode(\DOMElement $node)
    {
        $attr = $node->attributes;

        if ( $node->hasAttribute('change_time') ) {
            $this->changeTime = Response::stringToDateTime($attr->getNamedItem('change_time')->nodeValue);
        }

        if ( $node->hasAttribute('price_time') ) {
            $this->priceTime = Response::stringToDateTime($attr->getNamedItem('price_time')->nodeValue);
        }

        if ( $node->hasAttribute('edition_info') ) {
            $this->editionInfo = $attr->getNamedItem('edition_info')->nodeValue;
        }

        if ( $node->hasAttribute('language') ) {
            $this->language = $attr->getNamedItem('language')->nodeValue;
        }

        if ( $node->hasAttribute('physical_description_text') ) {
            $this->physicalDescText = $attr->getNamedItem('physical_description_text')->nodeValue;
        }

        if ( $node->hasAttribute('lcc_number') ) {
            $this->lccNumber = $attr->getNamedItem('lcc_number')->nodeValue;
        }

        if ( $node->hasAttribute('dewey_decimal_normalized') ) {
            $this->dewyDecimalNormalized = $attr->getNamedItem('dewey_decimal_normalized')->nodeValue;
        }

        if ( $node->hasAttribute('dewey_decimal') ) {
            $this->dewyDecimal = $attr->getNamedItem('dewey_decimal')->nodeValue;
        }
    }

}