<?php

/*
 * This file is part of the frenzy-framework package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Writer;


/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
use IronEdge\Component\FileUtils\File\Factory;

class FileWriter implements WriterInterface
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
     * This method writes the configuration to a file.
     *
     * @param array $data    - Data.
     * @param array $options - Options.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function write(array $data, array $options)
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

        $file = $this->_factory->createInstance($options['file'], null, $options);

        $file->setContents($data)
            ->save($options);
    }

}