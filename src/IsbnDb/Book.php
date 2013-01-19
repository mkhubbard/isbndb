<?php

namespace IsbnDb;

use IsbnDb\Exception\ResponseParserException;

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

    /**
     * @var array
     */
    protected $authors;


    /**
     * @static
     * Convert input string to title case.
     *
     * Original Title Case script: John Gruber <daringfireball.net>
     * Javascript port: David Gouch <individed.com>
     * PHP port of the above: Kroc Camen <camendesign.com>
     *
     * @param $title
     * @return string
     */
    public static function toTitleCase($title)
    {
        //find each word (including punctuation attached)
        preg_match_all ('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
        foreach ($m1[0] as &$m2) {
            //shorthand these- "match" and "index"
            list ($m, $i) = $m2;

            //correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
            //we fix this by recounting the text before the offset using multi-byte aware `strlen`
            $i = mb_strlen (substr ($title, 0, $i), 'UTF-8');

            //find words that should always be lowercase…
            //(never on the first word, and never if preceded by a colon)
            $m = $i>0 && mb_substr ($title, max (0, $i-2), 1, 'UTF-8') !== ':' &&
                !preg_match ('/[\x{2014}\x{2013}] ?/u', mb_substr ($title, max (0, $i-2), 2, 'UTF-8')) &&
                preg_match ('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
                ?	//…and convert them to lowercase
                mb_strtolower ($m, 'UTF-8')

                //else:	brackets and other wrappers
                : (	preg_match ('/[\'"_{(\[‘“]/u', mb_substr ($title, max (0, $i-1), 3, 'UTF-8'))
                    ?	//convert first letter within wrapper to uppercase
                    mb_substr ($m, 0, 1, 'UTF-8').
                        mb_strtoupper (mb_substr ($m, 1, 1, 'UTF-8'), 'UTF-8').
                        mb_substr ($m, 2, mb_strlen ($m, 'UTF-8')-2, 'UTF-8')

                    //else:	do not uppercase these cases
                    : (	preg_match ('/[\])}]/', mb_substr ($title, max (0, $i-1), 3, 'UTF-8')) ||
                        preg_match ('/[A-Z]+|&|\w+[._]\w+/u', mb_substr ($m, 1, mb_strlen ($m, 'UTF-8')-1, 'UTF-8'))
                        ?	$m
                        //if all else fails, then no more fringe-cases; uppercase the word
                        :	mb_strtoupper (mb_substr ($m, 0, 1, 'UTF-8'), 'UTF-8').
                            mb_substr ($m, 1, mb_strlen ($m, 'UTF-8'), 'UTF-8')
                    ));

            //resplice the title with the change (`substr_replace` is not multi-byte aware)
            $title = mb_substr ($title, 0, $i, 'UTF-8').$m.
                mb_substr ($title, $i+mb_strlen ($m, 'UTF-8'), mb_strlen ($title, 'UTF-8'), 'UTF-8')
            ;
        }

        //restore the HTML
        return $title;
    }

    public function __construct(\DOMElement $node)
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
        $this->changeTime = null;
        $this->priceTime = null;
        $this->editionInfo = '';
        $this->language = '';
        $this->physicalDescText = '';
        $this->lccNumber = '';
        $this->dewyDecimalNormalized = '';
        $this->dewyDecimal = '';
        $this->authors = array();

        $this->parse($node);
    }

    /**
     * @return null|string
     */
    public function getBookId()
    {
        return $this->bookId;
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
    public function getAuthorsText()
    {
        return $this->authorsText;
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
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function getUrlsText()
    {
        return $this->urlsText;
    }

    /**
     * @return string
     */
    public function getAwardsText()
    {
        return $this->awardsText;
    }

    /**
     * @return \DateTime|boolean
     */
    public function getChangeTime()
    {
        return $this->changeTime;
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
    public function getEditionInfo()
    {
        return $this->editionInfo;
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
    public function getPhysicalDescText()
    {
        return $this->physicalDescText;
    }

    /**
     * @return string
     */
    public function getLccNumber()
    {
        return $this->lccNumber;
    }

    /**
     * @param bool $normalized
     * @return string
     */
    public function getDewyDecimal($normalized = true)
    {
        if ( $normalized ) {
            return $this->dewyDecimalNormalized;
        } else {
            return $this->dewyDecimal;
        }
    }

    /**
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param string $text
     * @return string
     */
    private function scrubAuthorsText($text)
    {
        return rtrim(trim($text), ',');
    }

    private function parse(\DOMElement $node)
    {
        if (strtolower($node->tagName) !== 'bookdata') {
            throw new ResponseParserException('Invalid DOMElement passed to parser; expected tagName "BookData" received "' . $node->tagName . "");
        }

        $this->parseBookDataNode($node);

        foreach($node->childNodes as $child) {
            if (!is_a($child, 'DOMElement')) {
                continue;
            }

            switch(strtolower($child->tagName)) {
                case 'title':
                    $this->title = self::toTitleCase($child->nodeValue);
                    break;

                case 'titlelong':
                    $this->titleLong = self::toTitleCase($child->nodeValue);
                    break;

                case 'authorstext':
                    $this->authorsText = $this->scrubAuthorsText($child->nodeValue);
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

                case 'authors':
                    $this->parseAuthorsNode($child);
                    break;

                default:
                    // unhandled values are OK
            }
        }
    }

    private function parseBookDataNode(\DOMElement $node)
    {
        if ( $node->hasAttribute('book_id') ) {
            $this->bookId = trim($node->attributes->getNamedItem('book_id')->nodeValue);
        }

        if ( $node->hasAttribute('isbn') ) {
            $this->isbn = trim($node->attributes->getNamedItem('isbn')->nodeValue);
        }

        if ( $node->hasAttribute('isbn13') ) {
            $this->isbn13 = trim($node->attributes->getNamedItem('isbn13')->nodeValue);
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
            $this->editionInfo = trim($attr->getNamedItem('edition_info')->nodeValue);
        }

        if ( $node->hasAttribute('language') ) {
            $this->language = trim($attr->getNamedItem('language')->nodeValue);
        }

        if ( $node->hasAttribute('physical_description_text') ) {
            $this->physicalDescText = trim($attr->getNamedItem('physical_description_text')->nodeValue);
        }

        if ( $node->hasAttribute('lcc_number') ) {
            $this->lccNumber = trim($attr->getNamedItem('lcc_number')->nodeValue);
        }

        if ( $node->hasAttribute('dewey_decimal_normalized') ) {
            $this->dewyDecimalNormalized = trim($attr->getNamedItem('dewey_decimal_normalized')->nodeValue);
        }

        if ( $node->hasAttribute('dewey_decimal') ) {
            $this->dewyDecimal = trim($attr->getNamedItem('dewey_decimal')->nodeValue);
        }
    }

    private function parseAuthorsNode(\DOMElement $node)
    {
        foreach($node->childNodes as $child) {
            if (!is_a($child, 'DOMElement')) {
                continue;
            }

            if (strtolower($child->tagName) === 'person') {
                $this->authors[] = new Person($child);

            }
        }
    }

}