<?php


class IsbnDb_Tests_BookTest extends PHPUnit_Framework_TestCase
{

    public function testConstructionResponse()
    {
        $node = new DOMDocument();
        $node->loadXML(file_get_contents(__DIR__ . '/../../files/bookdata.xml'));

        $book = new IsbnDb\Book($node->documentElement);

        $this->assertEquals($book->getTitle(), 'The Walking Dead Compendium Volume 1');
        //$this->assertEquals($response->getPageSize(), 10);
        //$this->assertEquals($response->getResultCount(), 1);
        //$this->assertEquals($response->getResultTotal(), 1);
        //$this->assertEquals($response->getResultTotal(), count($response));
    }

}
