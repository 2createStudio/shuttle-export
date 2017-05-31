<?php

namespace ToCreateStudio\ShuttleExport;


/**
 * Plain text implementation. Uses standard file functions in PHP.
 */
class ShuttleDumpFilePlainText extends ShuttleDumpFile
{
    function open()
    {
        return fopen($this->file_location, 'w');
    }

    function write($string)
    {
        return fwrite($this->fh, $string);
    }

    function end()
    {
        return fclose($this->fh);
    }
}