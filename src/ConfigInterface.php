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

    /**
     * Sets this instance as read only or not.
     *
     * @param bool $bool - True or false.
     *
     * @return self
     */
    public function setReadOnly(bool $bool);

    /**
     * Is this instance read only?
     *
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * Setter method for field data.
     *
     * @param array $data                     - data.
     * @param bool  $replaceTemplateVariables - Replace template variables?
     *
     * @return $this
     */
    public function setData(array $data, $replaceTemplateVariables = true);

    /**
     * Getter method for field _data.
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Replaces the data with the template variables configured on this instance.
     *
     * @param string|array $data - Data.
     *
     * @return string|array
     */
    public function replaceTemplateVariables($data);

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
    public function get(string $index, $default = null, array $options = []);

    /**
     * Returns true if the parameter exists or false otherwise.
     *
     * @param string $index   - Index to search for.
     * @param array  $options - Options.
     *
     * @return bool
     */
    public function has(string $index, array $options = []): bool;

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
    public function set(string $index, $value, array $options = []);

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
    public function replaceRecursive(string $index, array $value, $default = null, array $options = []);

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
    public function mergeRecursive(string $index, array $value, $default = null, array $options = []);

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
    public function replace(string $index, array $value, $default = null, array $options = []);

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
    public function merge(string $index, array $value, $default = null, array $options = []);

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
    public function callFunction(
        string $function,
        string $index,
        array $value,
        $default = null,
        array $options = []
    );
}