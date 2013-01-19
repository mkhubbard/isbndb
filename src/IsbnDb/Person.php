<?php

namespace IsbnDb;

use IsbnDb\Exception\IsbnDbException;

class Person
{
    /** @var string */
    protected $personId;

    /** @var string */
    protected $name;

    public function __construct(\DOMElement $node)
    {
        $this->parse($node);
    }

    /**
     * @return string
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function parse(\DOMElement $node)
    {
        if (strtolower($node->tagName) !== 'person') {
            throw new IsbnDbException('Failed parsing Person node; expected tag "Person" received "' . $node->tagName . "'");
        }

        if ($node->hasAttribute('person_id')) {
            $this->personId = trim($node->attributes->getNamedItem('person_id')->nodeValue);
        }

        $this->name = trim($node->nodeValue);
    }

}
