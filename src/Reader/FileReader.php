<?php

/*
 * This file is part of the frenzy-framework package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Reader;


/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
use IronEdge\Component\FileUtils\File\Factory;

class FileReader implements ReaderInterface
{
    /**
     * Field _factory.
     *
     * @var
     */
    private $_factory;


    /**
     * Constructor.
     *
     * @param Factory|null $factory - Factory.
     */
    public function __construct(Factory $factory = null)
    {
        $this->_factory = $factory ?
            $factory :
            new Factory();
    }

    /**
     * This method reads the configuration from a file.
     *
     * @param array $options - Options.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function read(array $options)
    {
        if (!isset($options['file'])) {
            throw new \InvalidArgumentException(
                'Parameter "file" is mandatory.'
            );
        }

        if (!is_string($options['file'])) {
            throw new \InvalidArgumentException(
                'Parameter "file" must be a string.'
            );
        }

        if (!is_file($options['file']) || !is_readable($options['file'])) {
            throw new \InvalidArgumentException(
                'File "'.$options['file'].'" does not exist, or it\'s not readable.'
            );
        }

        $file = $this->_factory->createInstance($options['file'], null, $options);

        return $file->getContents();
    }

}