<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorTextInput extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $source    = $this->data->get('source', '');
        $delimiter = $this->data->get('delimiter', ',');
        $data      = array();
        if (!empty($source)) {
            $i = 0;
            $k = 0;
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $source) as $line) {
                if ($startIndex <= $i && ($count + $startIndex) > $i) {
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
        }
        return $data;
    }
}