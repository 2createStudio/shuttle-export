<?php

namespace ToCreateStudio\ShuttleExport;


/**
 * MySQL insert statement builder.
 */
/**
 * Class ShuttleInsertStatement
 * @package ToCreateStudio\ShuttleExport
 */
class ShuttleInsertStatement
{
    /**
     * @var array
     */
    private $rows = array();
    /**
     * @var int
     */
    private $length = 0;
    /**
     * @var
     */
    private $table;

    /**
     * ShuttleInsertStatement constructor.
     *
     * @param $table
     */
    function __construct($table)
    {
        $this->table = $table;
    }

    /**
     *
     */
    function reset()
    {
        $this->rows = array();
        $this->length = 0;
    }

    /**
     * @param $row
     */
    function add_row($row)
    {
        $row = '(' . implode(",", $row) . ')';
        $this->rows[] = $row;
        $this->length += strlen($row);
    }

    /**
     * @return bool|string
     */
    function get_sql()
    {
        if (empty($this->rows)) {
            return false;
        }

        return 'INSERT INTO `' . $this->table . '` VALUES ' .
            implode(",\n", $this->rows) . '; ';
    }

    /**
     * @return int
     */
    function get_length()
    {
        return $this->length;
    }
}