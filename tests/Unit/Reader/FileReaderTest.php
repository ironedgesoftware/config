<?php

/*
 * This file is part of the config package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Test\Unit\Reader;

use IronEdge\Component\Config\Reader\FileReader;
use IronEdge\Component\Config\Test\Unit\AbstractTestCase;

/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
class FileReaderTest extends AbstractTestCase
{
    /**
     * @expectedException \IronEdge\Component\Config\Exception\MissingOptionException
     */
    public function test_ifFileIsNotSetThrowException()
    {
        $reader = $this->createInstance();

        $reader->read([]);
    }

    /**
     * @expectedException \IronEdge\Component\Config\Exception\InvalidOptionTypeException
     */
    public function test_ifFileIsNotAStringThrowException()
    {
        $reader = $this->createInstance();

        $reader->read(['file' => []]);
    }

    /**
     * @expectedException \IronEdge\Component\Config\Exception\FileDoesNotExistException
     */
    public function test_ifFileDoesNotExistThrowException()
    {
        $reader = $this->createInstance();

        $reader->read(['file' => __DIR__.'/file_does_not_exist']);
    }

    // Helper Methods

    protected function createInstance()
    {
        return new FileReader();
    }
}