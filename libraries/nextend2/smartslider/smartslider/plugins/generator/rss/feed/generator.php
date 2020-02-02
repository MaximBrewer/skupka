<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorRSSFeed extends N2GeneratorAbstract
{

    private function get_http_response_code($url) {
        $headers = @get_headers($url);
        $return  = 'No content received from url:';
        if (is_array($headers)) {
            foreach ($headers AS $h) {
                if (substr($h, 0, 4) == 'HTTP') {
                    $return .= '<br>' . $h;
                }
            }
        } else {
            $return .= "<br>No response headers, as the url wouldn't even exist.";
        }
        return $return;
    }

    protected function _getData($count, $startIndex) {
        $url     = $this->data->get('rssurl', '');
        $date_format = $this->data->get('dateformat', 'Y-m-d');
        $sourceTranslate = $this->data->get('sourcetranslatedate', '');
        $translateValue  = explode('||', $sourceTranslate);
        $translate       = array();
        if ($sourceTranslate != 'January->January||February->February||March->March' && !empty($translateValue)) {
            foreach ($translateValue AS $tv) {
                $translateArray = explode('->', $tv);
                if (!empty($translateArray) && count($translateArray) == 2) {
                    $translate[$translateArray[0]] = $translateArray[1];
                }
            }
        }
        
        $content = @file_get_contents($url);
        if ($content === false) {
            N2Message::error(n2_($this->get_http_response_code($url)));
            return null;
        }
        try {
            $xml = new SimpleXmlElement($content);
        } catch (Exception $e) {
            N2Message::error(n2_('The data in the given url is not valid XML.'));
            return null;
        }
        $data = array();
        $i    = 0;
        foreach ($xml->channel->item as $entry) {
            foreach ($entry AS $key => $value) {
                $val = (string)$value;
                if (!empty($val)) {
                    if($this->checkIsAValidDate($val)){
                        $val = $this->translate(date($date_format, strtotime($val)), $translate);
                    }
                    $data[$i][$key] = $val;
                }
            }
            if (isset($entry->enclosure) && !empty($entry->enclosure->attributes()->url)) {
                $data[$i]['enclosure_url'] = (string)$entry->enclosure->attributes()->url;
            }
			$data[$i]['content'] = @(string)$entry->children('http://purl.org/rss/1.0/modules/content/')->encoded;
            $i++;
            if ($i == $count + $startIndex) break;
        }
        $data = array_slice($data, $startIndex, $count);
        return $data;
    }

    protected function checkIsAValidDate($dateString){
        return (bool)strtotime($dateString);
    }

    private function translate($from, $translate) {
        if (!empty($translate) && !empty($from)) {
            foreach ($translate AS $key => $value) {
                $from = str_replace($key, $value, $from);
            }
        }
        return $from;
    }
}
