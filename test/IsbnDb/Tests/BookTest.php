<?php


class IsbnDb_Tests_BookTest extends PHPUnit_Framework_TestCase
{

    public function testLoadState()
    {
        $node = new DOMDocument();
        $node->loadXML(file_get_contents(__DIR__ . '/../../files/bookdata.xml'));

        $book = new IsbnDb\Book($node->documentElement);

        $this->assertEquals('unit_test_book_id', $book->getBookId());
        $this->assertEquals('1607060760', $book->getIsbn());
        $this->assertEquals('9781607060765', $book->getIsbn13());
        $this->assertEquals('ISBNdb Client Library Unit Test', $book->getTitle());
        $this->assertEquals('ISBNdb Client Library Unit Test, First Edition', $book->getTitleLong());
        $this->assertEquals('John Doe, Jane Doe', $book->getAuthorsText());
        $this->assertEquals('unit_test_publisher_id', $book->getPublisherId());
        $this->assertEquals('Unit Test Publishing', $book->getPublisherText());
        $this->assertEquals('Unit test summary.', $book->getSummary());
        $this->assertEquals('Unit test notes.', $book->getNotes());
        $this->assertEquals('Unit test urls text.', $book->getUrlsText());
        $this->assertEquals('Unit test awards text.', $book->getAwardsText());
        $this->assertEquals(new \DateTime('2013-01-01T12:00:00Z'), $book->getChangeTime());
        $this->assertEquals(new \DateTime('2013-01-02T20:00:00Z'), $book->getPriceTime());
        $this->assertEquals('Paperback; 2013-01-01', $book->getEditionInfo());
        $this->assertEquals('PHP', $book->getLanguage());
        $this->assertEquals('6.9"x10.0"x2.0"; 4.9 lb; 1088 pages', $book->getPhysicalDescText());
        $this->assertEquals('KFW100.A75 S45 1997', $book->getLccNumber());
        $this->assertEquals('741.50', $book->getDewyDecimal());
        $this->assertEquals('741.50', $book->getDewyDecimal(true));
        $this->assertEquals('741/5', $book->getDewyDecimal(false));
        $this->assertEquals(2, count($book->getAuthors()));

        foreach($book->getAuthors() as $idx => $person) {
            $this->assertInstanceOf('IsbnDb\Person', $person);
            if ( $idx === 0 ) {
                $this->assertEquals('john_doe', $person->getPersonId());
                $this->assertEquals('Doe, John', $person->getName());
            }
            if ( $idx === 1 ) {
                $this->assertEquals('jane_doe', $person->getPersonId());
                $this->assertEquals('Doe, Jane', $person->getName());
            }
        }
    }

}
