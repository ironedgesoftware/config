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

/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
use IronEdge\Component\Config\Exception\ImportException;
use IronEdge\Component\Config\Exception\InvalidOptionTypeException;
use IronEdge\Component\Config\Reader\ReaderInterface;
use IronEdge\Component\Config\Writer\WriterInterface;

interface ConfigInterface
{
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
    public function replaceRecursive($index, array $value, $default = null, array $options = []);

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
    public function mergeRecursive($index, array $value, $default = null, array $options = []);

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
    public function replace($index, array $value, $default = null, array $options = []);

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
    public function merge($index, array $value, $default = null, array $options = []);

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
    public function callFunction($function, $index, array $value, $default = null, array $options = []);

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
    public function get($index, $default = null, array $options = []);

    /**
     * Returns true if the parameter exists or false otherwise.
     *
     * @param string $index   - Index to search for.
     * @param array  $options - Options.
     *
     * @return bool
     */
    public function has($index, array $options = []);

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
    public function set($index, $value, array $options = []);

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
    public function load(array $options = []);

    /**
     * Saves the configuration with the writer instance.
     *
     * @param array $options - Options.
     *
     * @return $this
     */
    public function save(array $options = []);

    /**
     * Getter method for field _data.
     *
     * @return array
     */
    public function getData();

    /**
     * Setter method for field data.
     *
     * @param array $data - data.
     *
     * @return $this
     */
    public function setData($data);

    /**
     * Getter method for field _options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Setter method for field options.
     *
     * @param array $options - options.
     *
     * @return $this
     */
    public function setOptions($options);

    /**
     * Returns a specific option, or $default if option $name does not exist.
     *
     * @param string $name    - Option name.
     * @param mixed  $default - Default.
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Returns the reader instance used by this object.
     *
     * @return ReaderInterface
     */
    public function getReader();

    /**
     * Sets the reader instance to use with this config object.
     *
     * @param ReaderInterface $reader - reader.
     *
     * @return $this
     */
    public function setReader($reader);

    /**
     * Returns the writer instance used by this object.
     *
     * @return WriterInterface
     */
    public function getWriter();

    /**
     * Sets the writer instance to use with this config object.
     *
     * @param WriterInterface $writer - writer.
     *
     * @return $this
     */
    public function setWriter($writer);
}