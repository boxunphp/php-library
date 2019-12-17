<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 1:56 PM
 */

namespace All\Exception;

class Exception extends \Exception
{
    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param mixed $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }
}