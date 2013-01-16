<?php


class IsbnDb_Tests_BookResponseTest extends PHPUnit_Framework_TestCase
{

    public function testSingleResponse() {
        $response = new IsbnDb\BookResponse(file_get_contents(__DIR__ . '/../../files/response.xml'));

        $this->assertEquals($response->getCurrentPage(), 1);
        $this->assertEquals($response->getPageSize(), 10);
        $this->assertEquals($response->getResultCount(), 1);
        $this->assertEquals($response->getResultTotal(), 1);
        $this->assertEquals($response->getResultTotal(), count($response));
    }

}
