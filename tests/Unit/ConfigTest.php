<?php

/*
 * This file is part of the config package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Test\Unit;


/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
use IronEdge\Component\Config\Config;

class ConfigTest extends AbstractTestCase
{
    public function test_load_loadInKeyLoadsDataInASpecificKey()
    {
        $config = $this->createInstance();

        $config->load(['data' => ['user' => ['username' => 'test']], 'loadInKey' => 'testComponent']);

        $this->assertEquals('test', $config->get('testComponent.user.username'));
    }




    // Helper Methods

    protected function createInstance(array $data = [], array $options = [])
    {
        $options = array_merge(
            [
                'reader'            => 'array',
                'writer'            => 'array'
            ],
            $options
        );

        return new Config($data, $options);
    }
}