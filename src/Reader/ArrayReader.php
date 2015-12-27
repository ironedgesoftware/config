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
class ArrayReader implements ReaderInterface
{
    /**
     * This method reads the configuration from an array.
     *
     * @param array $options - Options.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function read(array $options)
    {
        if (!isset($options['data'])) {
            throw new \InvalidArgumentException(
                'Parameter "data" is mandatory.'
            );
        }

        if (!is_array($options['data'])) {
            throw new \InvalidArgumentException(
                'Parameter "data" must be an array.'
            );
        }

        return $options['data'];
    }

}