<?php

/*
 * This file is part of the config package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Test\Integration;

use IronEdge\Component\Config\Config;

/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    // Helper Methods

    protected function createInstance(array $data = [], array $options = [])
    {
        $options = array_merge(
            [
                'reader'            => 'file',
                'writer'            => 'file'
            ],
            $options
        );

        return new Config($data, $options);
    }

    protected function getTmpPath()
    {
        return __DIR__.'/../tmp';
    }
}