<?php

use PHPUnit\Framework\TestCase;

use insma\otuspdf\PageCollection;
use insma\otuspdf\base\InvalidCallException;

final class PageCollectionTest extends TestCase
{
    private $pageCollectionClass = 'insma\otuspdf\PageCollection';
    private $pageClass = 'insma\otuspdf\Page';
    private $invalidCallExceptionClass = 'insma\otuspdf\base\InvalidCallException';

    public function testCanBeCreatedFromEmptyConfig()
    {
        $this->assertInstanceOf(
            $this->pageCollectionClass,
            new PageCollection()
        );
    }

    public function testAddReturnsPage()
    {
        $collection = new PageCollection();
        $this->assertInstanceOf(
            $this->pageClass,
            $collection->add()
        );
    }

    public function testCountPagesOnEmpty()
    {
        $collection = new PageCollection();

        $this->assertEquals(count($collection), 0);
    }

    public function testCountOnNotEmpty()
    {
        $collection = new PageCollection();
        $collection->add();
        $collection->add();
        $collection->add();

        $this->assertEquals(count($collection), 3);
    }

    public function testForeachPagesOnEmpty()
    {
        $collection = new PageCollection();

        foreach ($collection as $page) {
            $this->assertInstanceOf(
                $this->pageClass,
                $page
            );
        }

        $this->assertInstanceOf(
            $this->pageCollectionClass,
            new PageCollection()
        );
    }

    public function testForeachPagesOnNotEmpty()
    {
        $collection = new PageCollection();
        $expectedPages = [];
        $expectedPages[] = $collection->add();
        $expectedPages[] = $collection->add();

        $pages = [];
        foreach ($collection as $key => $page) {
            $this->assertInstanceOf(
                $this->pageClass,
                $page
            );
            $pages[] = $page;
        }

        $this->assertEquals($expectedPages, $pages);
    }
}
