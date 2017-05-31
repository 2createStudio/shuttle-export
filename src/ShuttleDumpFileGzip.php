<?php

namespace ToCreateStudio\ShuttleExport;

/**
 * Gzip implementation. Uses gz* functions.
 */
class ShuttleDumpFileGzip extends ShuttleDumpFile
{
    function open()
    {
        return gzopen($this->file_location, 'wb9');
    }

    function write($string)
    {
        return gzwrite($this->fh, $string);
    }

    function end()
    {
        return gzclose($this->fh);
    }
}