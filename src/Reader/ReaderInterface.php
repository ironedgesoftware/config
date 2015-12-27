<?php

/*
 * This file is part of the config package.
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
interface ReaderInterface
{
    /**
     * This method reads the configuration from a source.
     *
     * @param array $options - Options.
     *
     * @return array
     */
    public function read(array $options);
}