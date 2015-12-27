<?php

/*
 * This file is part of the config package.
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
interface WriterInterface
{
    /**
     * This method writes the configuration to a source.
     *
     * @param array $data    - Data.
     * @param array $options - Options.
     *
     * @return array
     */
    public function write(array $data, array $options);
}