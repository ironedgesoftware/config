<?php

/*
 * This file is part of the frenzy-framework package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config;

use IronEdge\Component\CommonUtils\Data\DataInterface;
use IronEdge\Component\CommonUtils\Data\DataTrait;
use IronEdge\Component\CommonUtils\Options\OptionsInterface;
use IronEdge\Component\Config\Exception\ImportException;
use IronEdge\Component\Config\Exception\InvalidOptionTypeException;
use IronEdge\Component\Config\Reader\ArrayReader;
use IronEdge\Component\Config\Reader\FileReader;
use IronEdge\Component\Config\Reader\ReaderInterface;
use IronEdge\Component\Config\Writer\WriterInterface;
use IronEdge\Component\Config\Writer\ArrayWriter;
use IronEdge\Component\Config\Writer\FileWriter;

/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
class Config implements ConfigInterface, DataInterface, OptionsInterface
{
    use DataTrait {
        setOptions as traitSetOptions;
    }


    /**
     * Reader instance.
     *
     * @var ReaderInterface
     */
    private $_reader;

    /**
     * Writer instance.
     *
     * @var WriterInterface
     */
    private $_writer;


    /**
     * Constructor.
     *
     * @param array $data    - Data.
     * @param array $options - Options.
     */
    public function __construct(array $data = [], array $options = [])
    {
        $this->setOptions($options)
            ->setData($data);
    }

    /**
     * Loads the configuration using the reader instance.
     *
     * @param array $options - Options.
     *
     * @throws InvalidOptionTypeException
     * @throws ImportException
     *
     * @return $this
     */
    public function load(array $options = [])
    {
        $options = array_replace_recursive(
            [
                'data'              => null,
                'file'              => null,
                'loadInKey'         => null,
                'processImports'    => false,
                'clearFirst'        => false,
                'readerOptions'     => []
            ],
            $options
        );

        // Simple shortcuts
        $options['readerOptions']['file'] = $options['readerOptions']['file'] ?? $options['file'];
        $options['readerOptions']['data'] = $options['readerOptions']['data'] ?? $options['data'];

        $reader = $this->getReader();
        $data = $reader->read($options['readerOptions']);

        if ($options['processImports'] && isset($data['import'])) {
            if (!is_array($data['import'])) {
                throw ImportException::create('"import" parameter must be an array.');
            }

            foreach ($data['import'] as $importData) {
                if (!is_array($importData)) {
                    throw ImportException::create('Each "import" array element must be an array.');
                }

                if (isset($importData['file'])) {
                    if ($options['file']) {
                        $importData['file'] = $importData['file']{0} === '/' ?
                            $importData['file'] :
                            dirname($options['file']).'/'.$importData['file'];
                    }

                    if (!is_file($importData['file'])) {
                        continue;
                    }
                }

                $readOptions = array_replace_recursive($options['readerOptions'], $importData);
                $data = array_replace_recursive($data, $reader->read($readOptions));
            }
        }

        if ($options['clearFirst']) {
            $this->setData([], false);
        }

        if ($options['loadInKey'] !== null) {
            if (!is_string($options['loadInKey'])) {
                throw InvalidOptionTypeException::create('loadInKey', 'string');
            }

            $this->set(
                $options['loadInKey'],
                $this->replaceTemplateVariables(array_replace_recursive($this->get($options['loadInKey'], []), $data))
            );
        } else {
            $this->setData(array_replace_recursive($this->getData(), $data));
        }

        /** @var Callable $callable */
        $callable = $this->getOption('onAfterLoad');

        if (!is_callable($callable)) {
            throw new \RuntimeException('Option "onAfterLoad" must be a callable.');
        }

        call_user_func_array($callable, [$this, $options]);

        return $this;
    }

    /**
     * Saves the configuration with the writer instance.
     *
     * @param array $options - Options.
     *
     * @return $this
     */
    public function save(array $options = [])
    {
        $options = array_merge(
            [
                'file'              => null,
                'writerOptions'     => []
            ],
            $options
        );

        // Simple shortcut
        $options['writerOptions']['file'] = $options['file'];

        /** @var Callable $callable */
        $callable = $this->getOption('onBeforeSave');

        if (!is_callable($callable)) {
            throw new \RuntimeException('Option "onBeforeSave" must be a callable.');
        }

        call_user_func_array($callable, [$this, $options]);

        $this->getWriter()->write($this->getData(), $options['writerOptions']);

        return $this;
    }

    /**
     * Returns the reader instance used by this object.
     *
     * @return ReaderInterface
     */
    public function getReader()
    {
        return $this->_reader;
    }

    /**
     * Sets the reader instance to use with this config object.
     *
     * @param ReaderInterface $reader - reader.
     *
     * @return $this
     */
    public function setReader($reader)
    {
        $this->_reader = $reader;

        return $this;
    }

    /**
     * Returns the writer instance used by this object.
     *
     * @return WriterInterface
     */
    public function getWriter()
    {
        return $this->_writer;
    }

    /**
     * Sets the writer instance to use with this config object.
     *
     * @param WriterInterface $writer - writer.
     *
     * @return $this
     */
    public function setWriter($writer)
    {
        $this->_writer = $writer;

        return $this;
    }

    /**
     * Executes initialization tasks.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->initializeReader();
        $this->initializeWriter();
    }

    /**
     * Initializes the reader instance.
     *
     * @return void
     */
    protected function initializeReader()
    {
        $reader = $this->getOption('reader');

        if (is_string($reader)) {
            switch ($reader) {
                case 'array':
                    $reader = new ArrayReader();

                    break;
                case 'file':
                    $reader = new FileReader();

                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Invalid reader "'.$reader.'". Valid reader strings: array, file.'
                    );
            }
        } else if (!is_object($reader) || !($reader instanceof ReaderInterface)) {
            throw new \InvalidArgumentException(
                'Option "reader" must be a string or an instance of IronEdge\Component\Config\Reader\ReaderInterface.'
            );
        }

        $this->setReader($reader);
    }

    /**
     * Initializes the writer instance.
     *
     * @return void
     */
    protected function initializeWriter()
    {
        $writer = $this->getOption('writer');

        if (is_string($writer)) {
            switch ($writer) {
                case 'array':
                    $writer = new ArrayWriter();

                    break;
                case 'file':
                    $writer = new FileWriter();

                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Invalid writer "'.$writer.'". Valid writer strings: array, file.'
                    );
            }
        } else if (!is_object($writer) || !($writer instanceof WriterInterface)) {
            throw new \InvalidArgumentException(
                'Option "writer" must be a string or an instance of IronEdge\Component\Config\Writer\WriterInterface.'
            );
        }

        $this->setWriter($writer);
    }

    /**
     * Sets options.
     *
     * @param array $options - Options.
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $ret = $this->traitSetOptions($options);

        $this->initialize();

        return $ret;
    }

    /**
     * Returns the default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return [
            'reader'                => 'file',
            'writer'                => 'file',
            'onAfterLoad'           => function(Config $config, array $options) {},
            'onBeforeSave'          => function(Config $config, array $options) {},
            'separator'             => '.',
            'templateVariables'     => []
        ];
    }


}