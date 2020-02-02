<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorTextText extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $source    = $this->data->get('sourcefile', '');
        $handle    = fopen($source, "r");
        $delimiter = $this->data->get('delimiter', ',');
        $data      = array();
        if ($handle) {
            $i = 0;
            $k = 0;
            while (($line = fgets($handle)) !== false && ($count + $startIndex) > $i) {
                if ($startIndex <= $i) {
                    $line  = rtrim($line, "\r\n");
                    $parts = explode($delimiter, $line);
                    $j     = 1;
                    foreach ($parts AS $part) {
                        $data[$k]['variable' . $j] = $part;
                        $j++;
                    }
                    $k++;
                }
                $i++;
            }

            fclose($handle);
        }
        return $data;
    }
}