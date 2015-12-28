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
    public function test_templateVariables_shouldBeReplacedAtAnyLevel()
    {
        $data = ['user' => array('email' => '%my_email%', 'profile' => array('age' => '%my_age%')), 'group' => '%my_group%'];
        $templateVariables = [
            '%my_email%'            => 'a@a.com',
            '%my_age%'              => 21,
            '%my_group%'            => 'admin_group'
        ];
        $config = $this->createInstance($data, ['templateVariables' => $templateVariables]);

        $this->assertEquals($templateVariables['%my_email%'], $config->get('user.email'));
    }

    public function test_has_shouldReturnCorrectElement()
    {
        $data = ['user' => array('email' => 'test@test.com', 'profile' => array('age' => 15)), 'group' => 'internal'];
        $config = $this->createInstance($data);

        $this->assertTrue($config->has('user.email'));
        $this->assertFalse($config->has('user.password'));
    }

    public function test_set_get_has_ifOtherSeparatorIsSpecifiedThenUseIt()
    {
        $config = $this->createInstance();

        $config->set('testComponent|user|username', 'test', ['separator' => '|']);

        $this->assertTrue($config->has('testComponent|user|username', ['separator' => '|']));
        $this->assertEquals('test', $config->get('testComponent|user|username', null, ['separator' => '|']));
    }

    public function test_load_loadInKeyLoadsDataInASpecificKey()
    {
        $config = $this->createInstance();

        $config->load(['data' => ['user' => ['username' => 'test']], 'loadInKey' => 'testComponent']);

        $this->assertEquals('test', $config->get('testComponent.user.username'));
    }

    public function test_get_shouldReturnCorrectElement()
    {
        $data = ['user' => array('email' => 'test@test.com', 'profile' => array('age' => 15)), 'group' => 'internal'];
        $config = $this->createInstance($data);

        $this->assertEquals($data['group'], $config->get('group'));
        $this->assertEquals($data['user']['email'], $config->get('user.email'));
        $this->assertEquals($data['user']['profile']['age'], $config->get('user.profile.age'));
        $this->assertEquals('notFound!', $config->get('user.username', 'notFound!'));
    }

    public function test_set_setsValuesCorrectly()
    {
        $data = ['user' => array('email' => 'test@test.com', 'profile' => array('age' => 15)), 'group' => 'internal'];
        $config = $this->createInstance($data);

        $config->set('test', 'myValue');
        $config->set('test2.test3.test4', 'myOtherValue');

        $this->assertEquals('myValue', $config->get('test'));
        $this->assertEquals('myOtherValue', $config->get('test2.test3.test4'));
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