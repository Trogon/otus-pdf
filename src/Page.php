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
namespace trogon\otuspdf;

use trogon\otuspdf\meta\PageInfo;
use trogon\otuspdf\PageBreak;
use trogon\otuspdf\Text;

class Page extends \trogon\otuspdf\base\DependencyObject
{
    private $info;
    private $blocks;

    public function __construct($config = [])
    {
        $this->info = new PageInfo($config);
        $this->blocks = new BlockCollection();
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getBlocks()
    {
        return $this->blocks;
    }

    public function addPagebreak($config = [])
    {
        return $this->blocks[] = new PageBreak($config);
    }

    public function addText($text, $config = [])
    {
        return $this->blocks[] = new TextBlock($text, $config);
    }
}
