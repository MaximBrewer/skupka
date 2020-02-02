<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorPicasaImages extends N2GeneratorAbstract {

    function _getData($count, $startIndex) {
        $album = $this->data->get('picasaalbums', 0);
        if (!empty($album)) {
            $explode  = explode("/", $album);
            $user_id  = $explode[2];
            $album_id = $explode[4];
            $random   = $this->data->get('picasarandom', 0);
            if (!$random) {
                $album .= "?start-index=" . ($startIndex + 1) . "&max-results=" . $count;
            }
            $client  = $this->info->getConfiguration()->getApi();
            $http    = new Google_Http_Request(
                'https://picasaweb.google.com/data/feed/api' . $album,
                'GET',
                array(
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Accept'       => '*/*'
                ),
                null
            );
            $request = $client->getAuth()->authenticatedRequest($http);

            $code       = $request->getResponseHttpCode();
            $body       = $request->getResponseBody();
            $sxml_album = @simplexml_load_string($body);

            $random = $this->data->get('picasarandom', 0);
            if ($sxml_album === false) {
                N2Message::error("No images were returned for this album.");
                return null;
            } else {
                $i    = 0;
                $j    = 0;
                $from = array(
                    "T",
                    "Z"
                );
                $to   = array(
                    " ",
                    ""
                );

                if ($random) {
                    $numbers = range(0, count($sxml_album->entry) - 1);
                    shuffle($numbers);
                    $randomArray = array_slice($numbers, 0, $count);
                }

                foreach ($sxml_album->entry as $album_photo) {
                    if (!$random || ($random && in_array($j, $randomArray))) {
                        $media                 = $album_photo->children('http://search.yahoo.com/mrss/');
                        $linkName              = (string)$media->group->content->attributes()->{'url'};
                        $data[$i]['image']     = $linkName . "?sz=0";
                        $data[$i]['thumbnail'] = $linkName;
                        $image_data            = get_object_vars($album_photo);

                        if (!is_object($image_data['summary'])) {
                            $data[$i]['title'] = $data[$i]['description'] = $image_data['summary'];
                        } else {
                            $data[$i]['title'] = $data[$i]['description'] = "";
                        }
                        $data[$i]['published'] = str_replace($from, $to, substr($image_data['published'], 0, -5));
                        $data[$i]['updated']   = str_replace($from, $to, substr($image_data['updated'], 0, -5));

                        $url                   = $image_data['link'][1]->attributes();
                        $data[$i]['url']       = (string)$url['href'];
                        $albumUrl              = explode('#', $url['href']);
                        $data[$i]['album_url'] = $albumUrl[0];

                        $data[$i]['google_plus_album_url'] = "https://plus.google.com/photos/" . $user_id . "/albums/" . $album_id;
                        $i++;
                    }
                    $j++;
                }
            }
            if ($random) {
                shuffle($data);
            }
            return $data;
        } else {
            N2Message::error("There isn't an album selected.");
            return null;
        }
    }
}
