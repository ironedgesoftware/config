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
class Config implements ConfigInterface
{
    /**
     * The data hold by this instance.
     *
     * @var array
     */
    private $_data;

    /**
     * Options.
     *
     * @var array
     */
    private $_options;

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
        $options = array_replace_recursive(
            [
                'reader'                => 'file',
                'writer'                => 'file',
                'onAfterLoad'           => function(Config $config, array $options) {},
                'onBeforeSave'          => function(Config $config, array $options) {},
                'separator'             => '.',
                'templateVariables'     => []
            ],
            $options
        );

        $this->setOptions($options)
            ->setData($data);
    }

    /**
     * Returns an element of the configuration array. You can search for values recursively using
     * a dot (or the separator set on the "separator" option). For example: user.email would look
     * for the value in the array like this: $data['user']['email'].
     *
     * @param string $index   - Index to search for.
     * @param mixed  $default - Default.
     * @param array  $options - Options.
     *
     * @return mixed
     */
    public function get($index, $default = null, array $options = [])
    {
        $separator = isset($options['separator']) ?
            $options['separator'] :
            $this->getOption('separator');
        $value = $this->getData();
        $keys = explode($separator, $index);

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                $value = $default;

                break;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Returns true if the parameter exists or false otherwise.
     *
     * @param string $index   - Index to search for.
     * @param array  $options - Options.
     *
     * @return bool
     */
    public function has($index, array $options = [])
    {
        $separator = isset($options['separator']) ?
            $options['separator'] :
            $this->getOption('separator');
        $value = $this->getData();
        $keys = explode($separator, $index);

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return false;
            }

            $value = $value[$key];
        }

        return true;
    }

    /**
     * Sets an element of the configuration. It allows to set elements recursively. For example,
     * if you set the key "user.email" with value "a@a.com", the result is similar to the following:
     *
     * $data['user']['email'] = 'a@a.com'
     *
     * If some key does not exist, we will create it for you.
     *
     * @param string $index   - Parameter index.
     * @param mixed  $value   - Parameter value.
     * @param array  $options - Options.
     *
     * @return $this
     */
    public function set($index, $value, array $options = [])
    {
        $separator = isset($options['separator']) ?
            $options['separator'] :
            $this->getOption('separator');
        $root = &$this->_data;
        $keys = explode($separator, $index);
        $count = count($keys);

        foreach ($keys as $i => $key) {
            if ($i === ($count - 1)) {
                $root[$key] = $value;

                break;
            }

            if (!is_array($root) || !array_key_exists($key, $root)) {
                $root[$key] = [];
            }

            $root = &$root[$key];
        }

        return $this;
    }

    /**
     * Calls array_replace_recursive using the data existent on $index and data on $value.
     *
     * @param string $index   - Index.
     * @param array  $value   - Value.
     * @param mixed  $default - Default value.
     * @param array  $options - Options.
     *
     * @return $this
     */
    public function replaceRecursive($index, array $value, $default = null, array $options = [])
    {
        return $this->callFunction('array_replace_recursive', $index, $value, $default, $options);
    }

    /**
     * Calls array_merge_recursive using the data existent on $index and data on $value.
     *
     * @param string $index   - Index.
     * @param array  $value   - Value.
     * @param mixed  $default - Default value.
     * @param array  $options - Options.
     *
     * @return $this
     */
    public function mergeRecursive($index, array $value, $default = null, array $options = [])
    {
        return $this->callFunction('array_merge_recursive', $index, $value, $default, $options);
    }

    /**
     * Calls array_replace using the data existent on $index and data on $value.
     *
     * @param string $index   - Index.
     * @param array  $value   - Value.
     * @param mixed  $default - Default value.
     * @param array  $options - Options.
     *
     * @return $this
     */
    public function replace($index, array $value, $default = null, array $options = [])
    {
        return $this->callFunction('array_replace', $index, $value, $default, $options);
    }

    /**
     * Calls array_merge using the data existent on $index and data on $value.
     *
     * @param string $index   - Index.
     * @param array  $value   - Value.
     * @param mixed  $default - Default value.
     * @param array  $options - Options.
     *
     * @return $this
     */
    public function merge($index, array $value, $default = null, array $options = [])
    {
        return $this->callFunction('array_merge', $index, $value, $default, $options);
    }

    /**
     * Obtains data on $index, calls $function using as first parameter the data obtained, and as second
     * parameter $value.
     *
     * @param string $function - Function to call.
     * @param string $index    - Index.
     * @param array  $value    - Value.
     * @param mixed  $default  - Default value.
     * @param array  $options  - Options.
     *
     * @return $this
     */
    public function callFunction($function, $index, array $value, $default = null, array $options = [])
    {
        if (!function_exists($function)) {
            throw new \RuntimeException('Function "'.$function.'" does not exist!');
        }

        $data = $this->get($index, $default, $options);

        $value = $function($data, $value);

        $this->set($index, $value, $options);

        return $this;
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
        $options['readerOptions']['file'] = isset($options['readerOptions']['file']) ?
            $options['readerOptions']['file'] :
            $options['file'];
        $options['readerOptions']['data'] = isset($options['readerOptions']['data']) ?
            $options['readerOptions']['data'] :
            $options['data'];

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
     * Getter method for field _data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Setter method for field data.
     *
     * @param array $data                     - data.
     * @param bool  $replaceTemplateVariables - Replace template variables?
     *
     * @return $this
     */
    public function setData($data, $replaceTemplateVariables = true)
    {
        if ($replaceTemplateVariables) {
            $data = $this->replaceTemplateVariables($data);
        }

        $this->_data = $data;

        return $this;
    }

    /**
     * Replaces the data with the template variables configured on this instance.
     *
     * @param string|array $data - Data.
     *
     * @return string|array
     */
    public function replaceTemplateVariables($data)
    {
        if ($templateVariables = $this->getOption('templateVariables', [])) {
            $templateVariableKeys = array_keys($templateVariables);

            if (is_string($data)) {
                $data = str_replace($templateVariableKeys, $templateVariables, $data);
            } else if (is_array($data)) {
                array_walk_recursive(
                    $data,
                    function(&$value, &$key, &$data) {
                        $value = str_replace($data['keys'], $data['values'], $value);
                    },
                    [
                        'keys'      => $templateVariableKeys,
                        'values'    => $templateVariables
                    ]
                );
            }
        }

        return $data;
    }

    /**
     * Getter method for field _options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Setter method for field options.
     *
     * @param array $options - options.
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->_options = $options;

        $this->initialize();

        return $this;
    }

    /**
     * Returns a specific option, or $default if option $name does not exist.
     *
     * @param string $name    - Option name.
     * @param mixed  $default - Default.
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return array_key_exists($name, $this->_options) ?
            $this->_options[$name] :
            $default;
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
}