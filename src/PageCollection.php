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

use ArrayIterator;

use trogon\otuspdf\Page;

class PageCollection extends \trogon\otuspdf\base\DependencyObject
    implements \ArrayAccess, \Countable, \IteratorAggregate
{
    private $container;
    private $position;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->container = [];
    }

    public function add($config = [])
    {
        $page = new Page($config);
        $this->container[] = $page;
        return $page;
    }

    /* ArrayAccess Methods */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /* Countable Methods */
    public function count()
    {
        return count($this->container);
    }

    /* IteratorAggregate Methods */
    public function getIterator()
    {
        return new ArrayIterator($this->container);
    }
}
