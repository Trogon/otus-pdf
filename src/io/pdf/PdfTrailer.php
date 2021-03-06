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
namespace trogon\otuspdf\io\pdf;

use trogon\otuspdf\io\pdf\PdfDictionary;

class PdfTrailer extends \trogon\otuspdf\base\DependencyObject
{
    private $content;
    private $xrefOffset;

    public function init()
    {
        parent::init();
        $this->content = new PdfDictionary();
        if (is_null($this->xrefOffset)) {
            $this->xrefOffset = 0;
        }
    }

    public function getXrefOffset()
    {
        return $this->xrefOffset;
    }

    public function setXrefOffset($xrefOffset)
    {
        $this->xrefOffset = $xrefOffset;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function toString()
    {
        $content = "trailer\n";
        $content .= $this->content->toString() . "\n";
        $content .= "startxref\n";
        $content .= "{$this->xrefOffset}";
        return $content;
    }
}
