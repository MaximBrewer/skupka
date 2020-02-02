<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFlickrPhotosSearch extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $client = $this->info->getConfiguration()
                             ->getApi();

        $userID  = $this->data->get('userID', 'me');
        $tags    = $this->data->get('tags', '');
        $text    = $this->data->get('text', '');
        $privacy = $this->data->get('privacy', '1');
        $type    = $this->data->get('type', '1');

        $args   = array(
            'tags'           => $tags,
            'user_id'        => $userID,
            'text'           => $text,
            'privacy_filter' => $privacy,
            'content_type'   => $type,
            'per_page'       => $count
        );
        $result = $client->photos_search($args);
        if (is_array($result['photo']) && !empty($result['photo'])) {
            $photos = array_slice($result['photo'], $startIndex, $count);
        } else {
            return null;
        }

        $data = array();
        foreach ($photos AS $photo) {
            if (!isset($ow)) {
                $ow = $client->people_getInfo($photo['owner']);
            }
            $image  = 'https://c2.staticflickr.com/' . $photo['farm'] . '/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'];
            $r      = array(
                'image'     => $image . '_b.jpg',
                'thumbnail' => $image . '_m.jpg',
                'title'     => $photo['title'],
                'url'       => 'https://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'],
                'url_b'     => $image . '_b.jpg',
                'url_c'     => $image . '_c.jpg',
                'url_h'     => $image . '_h.jpg',
                'url_m'     => $image . '_m.jpg',
                'url_n'     => $image . '_n.jpg',
                'url_s'     => $image . '_s.jpg',
                'url_t'     => $image . '_t.jpg',
                'url_q'     => $image . '_q.jpg',
                'url_z'     => $image . '_z.jpg'
            );
            $data[] = $r;
        }
        return $data;
    }
}