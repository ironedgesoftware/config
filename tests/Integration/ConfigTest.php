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


/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
class ConfigTest extends AbstractTestCase
{
    public function test_importsWorksAsExpected()
    {
        $config = $this->createInstance();

        $config->load(['file' => $this->getConfigPath().'/config.yml', 'processImports' => true]);

        $this->assertEquals('value', $config->get('testParam1.testParam2.testParam3'));
        $this->assertEquals('/my/route.html', $config->get('routes.myRoute'));
        $this->assertEquals('MyClass', $config->get('services.myService.class'));
    }
}