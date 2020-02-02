<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2Generator500pxCollection extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $data   = array();
        $client = $this->info->getConfiguration()
                             ->getApi();

        $collectionID = $this->data->get('collection');
        $responseCode = $client->request('GET', $client->url('collections/' . $collectionID), array(
            'image_size' => array(
                1,
                2,
                3,
                4
            )
        ));
        if ($responseCode == 200) {
            $r      = json_decode($client->response['response'], true);
            $photos = $r['photos'];

            $photos = array_slice($photos, $startIndex, $count);

            foreach ($photos AS $photo) {
                $p      = array(
                    'image'       => $photo['images'][3]['url'],
                    'thumbnail'   => $photo['images'][2]['url'],
                    'title'       => $photo['name'],
                    'description' => $photo['description'],
                    'url'         => 'https://500px.com' . $photo['url'],
                    'url_label'   => n2_('View')
                );
                $data[] = $p;
            }
        }
        return $data;
    }
}