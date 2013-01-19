<?php

class IsbnDb_Tests_PersonTest extends PHPUnit_Framework_TestCase
{

    public function testLoadState()
    {
        $node = new DOMDocument();
        $node->loadXML(file_get_contents(__DIR__ . '/../../files/person.xml'));

        $person = new IsbnDb\Person($node->documentElement);

        $this->assertEquals('john_doe', $person->getPersonId());
        $this->assertEquals('Doe, John', $person->getName());

    }

}