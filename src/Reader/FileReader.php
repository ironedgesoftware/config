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
use IronEdge\Component\Config\Exception\FileDoesNotExistException;
use IronEdge\Component\Config\Exception\FileIsNotReadableException;
use IronEdge\Component\Config\Exception\InvalidOptionTypeException;
use IronEdge\Component\Config\Exception\MissingOptionException;
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
     * @throws MissingOptionException
     * @throws InvalidOptionTypeException
     * @throws FileDoesNotExistException
     * @throws FileIsNotReadableException
     *
     * @return array
     */
    public function read(array $options)
    {
        if (!isset($options['file'])) {
            throw MissingOptionException::create('file');
        }

        if (!is_string($options['file'])) {
            throw InvalidOptionTypeException::create('file', 'string');
        }

        if (!is_file($options['file'])) {
            throw FileDoesNotExistException::create($options['file']);
        }

        if (!is_readable($options['file'])) {
            throw FileIsNotReadableException::create($options['file']);
        }

        $file = $this->_factory->createInstance($options['file'], null, $options);

        return $file->getContents();
    }

}