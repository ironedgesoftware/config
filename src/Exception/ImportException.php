<?php

/*
 * This file is part of the frenzy-framework package.
 *
 * (c) Gustavo Falco <comfortablynumb84@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IronEdge\Component\Config\Exception;


/*
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 */
class ImportException extends BaseException
{
    public static function create($msg)
    {
        return new self($msg);
    }
}