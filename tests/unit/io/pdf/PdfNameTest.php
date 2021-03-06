<?php
namespace trogon\otuspdf\test\unit\io\pdf;

use PHPUnit\Framework\TestCase;

use trogon\otuspdf\io\pdf\PdfName;
use trogon\otuspdf\base\InvalidOperationException;

/**
 * @covers \trogon\otuspdf\io\pdf\PdfName
 */
final class PdfNameTest extends TestCase
{
    private $pdfNameClass = 'trogon\otuspdf\io\pdf\PdfName';
    private $invalidOperationExceptionClass = 'trogon\otuspdf\base\InvalidOperationException';

    public function testCanBeCreatedFromEmptyConfig()
    {
        $this->assertInstanceOf(
            $this->pdfNameClass,
            new PdfName()
        );
    }

    public function testCanBeCreatedFromAllConfig()
    {
        $this->assertInstanceOf(
            $this->pdfNameClass,
            new PdfName([
                'value' => 'testname'
            ])
        );
    }

    public function testToStringReturnsValidPdfNameStringForDefinedName()
    {
        $pdfName = new PdfName([
            'value' => 'testname'
        ]);

        $this->assertEquals(
            '/testname',
            $pdfName->toString()
        );
    }
}
