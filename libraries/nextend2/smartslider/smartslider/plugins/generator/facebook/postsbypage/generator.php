<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFacebookPostsByPage extends N2GeneratorAbstract {

    protected function _getData($count, $startIndex) {

        $api = $this->info->getConfiguration()
                          ->getApi();

        $data = array();
        try {
            $result = $api->sendRequest('GET', $this->data->get('page', 'nextendweb') . '/' . $this->data->get('endpoint', 'feed'), array(
                'offset' => $startIndex,
                'limit'  => $count,
                'fields' => implode(',', array(
                    'from',
                    'link',
                    'picture',
                    'source',
                    'description',
                    'message',
                    'story',
                    'type',
                    'full_picture'
                ))
            ))
                          ->getDecodedBody();

            for ($i = 0; $i < count($result['data']); $i++) {
                $post                  = $result['data'][$i];
                $record['link']        = isset($post['link']) ? $post['link'] : '';
                $record['description'] = isset($post['message']) ? str_replace("\n", "<br/>", $this->makeClickableLinks($post['message'])) : '';
                $record['message']     = $record['description'];
                $record['story']       = isset($post['story']) ? $this->makeClickableLinks($post['story']) : '';
                $record['type']        = $post['type'];
                $record['image']       = isset($post['full_picture']) ? $post['full_picture'] : '';

                $data[$i] = &$record;
                unset($record);
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }
}