<?php
/**
 * Otus PDF - PDF document generation library
 * Copyright(C) 2019 Maciej Klemarczyk
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
namespace insma\otuspdf\io;

use insma\otuspdf\io\pdf\PdfArray;
use insma\otuspdf\io\pdf\PdfCrossReference;
use insma\otuspdf\io\pdf\PdfDictionary;
use insma\otuspdf\io\pdf\PdfName;
use insma\otuspdf\io\pdf\PdfNumber;
use insma\otuspdf\io\pdf\PdfObject;
use insma\otuspdf\io\pdf\PdfObjectFactory;
use insma\otuspdf\io\pdf\PdfObjectReference;
use insma\otuspdf\io\pdf\PdfStream;
use insma\otuspdf\io\pdf\PdfString;
use insma\otuspdf\io\pdf\PdfTrailer;

class DocumentWriter extends \insma\otuspdf\base\BaseObject
{
    private $document;
    private $objectFactory;
    private $crossReference;
    private $trailer;
    private $offset;
    private $content;

    public function __construct(\insma\otuspdf\Document $document)
    {
        $this->document = $document;
        $this->objectFactory = new PdfObjectFactory();
        $this->crossReference = new PdfCrossReference();
        $this->trailer = new PdfTrailer();
        $this->offset = 0;
        $this->content = '';
    }

    private function generatePdfContent()
    {
        $objects = [];

        // PDF Catalog
        $objects[0] = $this->objectFactory->create();
        $objects[0]->content = new PdfDictionary();
        $objects[0]->content->addItem(
            new PdfName(['value' => 'Type']),
            new PdfName(['value' => 'Catalog'])
        );

        // PDF Outlines
        $objects[1] = $this->objectFactory->create();
        $objects[1]->content = new PdfDictionary();
        $objects[1]->content->addItem(
            new PdfName(['value' => 'Type']),
            new PdfName(['value' => 'Outlines'])
        );
        $objects[1]->content->addItem(
            new PdfName(['value' => 'Count']),
            new PdfNumber(['value' => 0])
        );
        $objects[0]->content->addItem(
            new PdfName(['value' => 'Outlines']),
            new PdfObjectReference(['object' => $objects[1]])
        );

        // PDF Pages collection
        $objects[2] = $this->objectFactory->create();
        $objects[2]->content = new PdfDictionary();
        $objects[2]->content->addItem(
            new PdfName(['value' => 'Type']),
            new PdfName(['value' => 'Pages'])
        );
        $pageRefs = new PdfArray();
        $objects[2]->content->addItem(
            new PdfName(['value' => 'Kids']),
            $pageRefs
        );
        $objects[2]->content->addItem(
            new PdfName(['value' => 'Count']),
            new PdfNumber(['value' => 1])
        );
        $resourcesDict = new PdfDictionary();
        $objects[2]->content->addItem(
            new PdfName(['value' => 'Resources']),
            $resourcesDict
        );
        $pageSizeArray = new PdfArray();
        $objects[2]->content->addItem(
            new PdfName(['value' => 'MediaBox']),
            $pageSizeArray
        );
        $objects[0]->content->addItem(
            new PdfName(['value' => 'Pages']),
            new PdfObjectReference(['object' => $objects[2]])
        );

        $pageSizeArray->addItem(new PdfNumber(['value' => (0 * 72)]));
        $pageSizeArray->addItem(new PdfNumber(['value' => (0 * 72)]));
        $pageSizeArray->addItem(new PdfNumber(['value' => (8.27 * 72)]));
        $pageSizeArray->addItem(new PdfNumber(['value' => (11.7 * 72)]));

        // PDF Proc Set
        $objects[3] = $this->objectFactory->create();
        $objects[3]->content = new PdfArray();
        $objects[3]->content->addItem(new PdfName(['value' => 'PDF']));
        $resourcesDict->addItem(
            new PdfName(['value' => 'ProcSet']),
            new PdfObjectReference(['object' => $objects[3]])
        );

        // PDF Page 1
        $objects[5] = $this->objectFactory->create();
        $objects[5]->content = new PdfDictionary();
        $objects[5]->content->addItem(
            new PdfName(['value' => 'Type']),
            new PdfName(['value' => 'Page'])
        );
        $objects[5]->content->addItem(
            new PdfName(['value' => 'Parent']),
            new PdfObjectReference(['object' => $objects[2]])
        );
        $pageRefs->addItem(new PdfObjectReference(['object' => $objects[5]]));

        // PDF Page 1 content
        $objects[6] = $this->objectFactory->create();
        $objects[6]->content = new PdfDictionary();
        $objects[6]->stream = new PdfStream();
        $objects[6]->stream->value = '';
        $objects[6]->content->addItem(
            new PdfName(['value' => 'Length']),
            new PdfNumber(['value' => $objects[6]->stream->length])
        );
        $objects[5]->content->addItem(
            new PdfName(['value' => 'Contents']),
            new PdfObjectReference(['object' => $objects[6]])
        );

        $docInfoObject = null;
        if (!empty($this->document->info)) {
            $infoDict = new PdfDictionary();
            $this->setInfoTextIfNotEmpty($infoDict, 'title', 'Title');
            $this->setInfoTextIfNotEmpty($infoDict, 'author', 'Author');
            $this->setInfoTextIfNotEmpty($infoDict, 'subject', 'Subject');
            $this->setInfoTextIfNotEmpty($infoDict, 'keywords', 'Keywords');
            $this->setInfoTextIfNotEmpty($infoDict, 'creator', 'Creator');
            $this->setInfoTextIfNotEmpty($infoDict, 'producer', 'Producer');
            $this->setInfoTextIfNotEmpty($infoDict, 'creationDate', 'CreationDate');
            $this->setInfoTextIfNotEmpty($infoDict, 'modificationDate', 'ModDate');
            if (!empty($infoDict->items)) {
                $objects[4] = $this->objectFactory->create();
                $objects[4]->content = $infoDict;
                $docInfoObject = $objects[4];
            }
        }

        $this->writeHeader();
        $this->writeBody($objects);
        $this->writeCrossReference();
        $this->writeTrailer($objects[0], $docInfoObject);
    }

    private function setInfoTextIfNotEmpty($dictionary, $property, $pdfKey)
    {
        if (!empty($this->document->info->$property)) {
            $dictionary->addItem(
                new PdfName(['value' => $pdfKey]),
                new PdfString(['value' => $this->document->info->$property])
            );
        }
    }

    public function save(String $filepath)
    {
        if (empty($this->content)) {
            $this->generatePdfContent();
        }

        $fp = fopen($filepath, 'w');
        $encodedContent = $this->encodeContent($this->content);
        fwrite($fp, $encodedContent);
        fclose($fp);
    }

    public function toString()
    {
        if (empty($this->content)) {
            $this->generatePdfContent();
        }

        $encodedContent = $this->encodeContent($this->content);
        echo($encodedContent);
    }

    private function writeHeader()
    {
        $this->writeLine('%PDF-1.7');
    }

    private function writeBody($objects)
    {
        $this->offset = \strlen($this->content);
        foreach ($objects as $object) {
            $text = $object->toString();
            $this->crossReference->registerObject($object, $this->offset);
            $this->writeLine($text);
            $this->offset += \strlen($text);
        }
    }

    private function writeCrossReference()
    {
        $this->trailer->xrefOffset = $this->offset;
        $this->writeLine($this->crossReference->toString());
    }

    private function writeTrailer($rootObject, $docInfoObject = null)
    {
        $this->trailer->content->addItem(
            new PdfName(['value' => 'Size']),
            new PdfNumber(['value' => $this->crossReference->size])
        );
        $this->trailer->content->addItem(
            new PdfName(['value' => 'Root']),
            new PdfObjectReference(['object' => $rootObject])
        );
        if ($docInfoObject instanceof PdfObject) {
            $this->trailer->content->addItem(
                new PdfName(['value' => 'Info']),
                new PdfObjectReference(['object' => $docInfoObject])
            );
        }

        $this->writeLine($this->trailer->toString());
        $this->writeLine('%%EOF');
    }

    private function writeLine($line)
    {
        $this->content .= $line. "\n";
    }

    private function encodeContent($data)
    {
        $encodedData = \iconv(\mb_detect_encoding($data), 'ISO-8859-1//TRANSLIT', $data);
        if ($encodedData === false) {
            throw new \Exception('Convesion failed');
        }
        return $encodedData;
    }
}
