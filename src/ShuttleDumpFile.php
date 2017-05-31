<?php

namespace ToCreateStudio\ShuttleExport;


abstract class ShuttleDumpFile
{
    /**
     * File Handle
     */
    protected $fh;

    /**
     * Location of the dump file on the disk
     */
    protected $file_location;

    abstract function write($string);

    abstract function end();

    static function create($filename)
    {
        if (self::is_gzip($filename)) {
            return new ShuttleDumpFileGzip($filename);
        }
        return new ShuttleDumpFilePlainText($filename);
    }

    function __construct($file)
    {
        $this->file_location = $file;
        $this->fh = $this->open();

        if (!$this->fh) {
            throw new ShuttleException("Couldn't create gz file");
        }
    }

    public static function is_gzip($filename)
    {
        return preg_match('~gz$~i', $filename);
    }
}