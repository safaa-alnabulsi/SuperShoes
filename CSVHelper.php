<?php

/**
 * This class reads and write CSV files
 *
 * @author Safaa AlNabulsi
 */
class CSVHelper
{

    /**
     * full path to the file
     * @var string  
     */
    private $file;

    /**
     * Array holds the header of the file
     * @var array 
     */
    private $header;

    function __construct($file)
    {
        $this->setFile($file);
    }

    private function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    private function setHeader($header)
    {
        $this->header = $header;
    }

    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Open the file for the given option
     * @param string $file
     * @param string $option r/a/w
     * @return file
     */
    private function open($file, $option)
    {
        return fopen($file, $option);
    }

    /**
     * Close the csv file 
     * @param string $file
     */
    private function close($file)
    {
        fclose($file);
    }

    /**
     * print Message 
     */
    private function printMessage($message)
    {
        echo "<pre>" . $message . "</pre>";
    }

    /**
     * reads the file line by line and put it in an array
     * @return array holds the lines of the file 
     */
    public function read()
    {
        $file = $this->open($this->getFile(), 'r');
        $lines = array();
        while (($line = fgetcsv($file)) !== FALSE) {
            if ($this->getHeader() == null) {
                $this->setHeader($line);
            } else {
                $lines[] = $line;
            }
        }
        $this->close($file);
        return $lines;
    }

    /**
     * writes the array into the file
     * @param array $lines lines we want to write in the file
     */
    public function write($lines)
    {
        $file = $this->open($this->getFile(), 'w');
        foreach ($lines as $line) {
            fputcsv($file, $line);
        }

        $this->close($file);
        $this->printMessage('Data has been written to file: '.$this->getFile());
    }

}

?>
