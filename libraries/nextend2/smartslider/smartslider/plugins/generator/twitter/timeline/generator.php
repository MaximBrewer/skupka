<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorTwitterTimeline extends N2GeneratorAbstract
{

    private $resultPerPage = 50;
    private $pages = array();
    private $client;

    protected function _getData($count, $startIndex) {
        $this->client = $this->info->getConfiguration()
                                   ->getApi();

        $data = array();
        try {

            $offset = $startIndex;
            $limit  = $count;
            for ($i = 0, $j = $offset; $j < $offset + $limit; $i++, $j++) {

                $items = $this->getPage(intval($j / $this->resultPerPage));

                if (isset($items[$j % $this->resultPerPage])) {
                    $item = $items[$j % $this->resultPerPage];
                }
                
                if (empty($item)) {
                    // There is no more item in the list
                    break;
                }
                $record['author_name']  = $item['user']['screen_name'];
                $record['author_url']   = $item['user']['url'];
                $record['author_image'] = str_replace('_normal.', '.', $item['user']['profile_image_url_https']);
                $record['message']      = $this->makeClickableLinks($item['text']);
                $item['id']             = number_format($item['id'], 0, '.', '');
                $record['url']          = 'https://twitter.com/' . $item['user']['id'] . '/status/' . $item['id'];
                $record['url_label']    = n2_('View tweet');

                if (isset($item['entities']['media'][0]['media_url'])) {
                    $record['tweet_image'] = $item['entities']['media'][0]['media_url'];
                }

                $record['userid']           = $item['user']['id'];
                $record['user_name']        = $item['user']['name'];
                $record['user_description'] = $item['user']['description'];
                $record['user_location']    = $item['user']['location'];

                if (isset($item['retweeted_status'])) {
                    $record['tweet_author_name']  = $item['retweeted_status']['user']['screen_name'];
                    $record['tweet_author_image'] = str_replace('_normal.', '.', $item['retweeted_status']['user']['profile_image_url_https']);
                } else {
                    $record['tweet_author_name']  = $record['author_name'];
                    $record['tweet_author_image'] = $record['author_image'];
                }

                $data[$i] = &$record;
                unset($record);

            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }

    private function getPage($page) {
        if (!isset($this->pages[$page])) {
            $request = array(
                'count' => $this->resultPerPage,
                'include_rts' => $this->data->get('retweets', '1'),
                'exclude_replies' => $this->data->get('replies', '0')
            );
            if ($page != 0) {
                $previousPage      = $this->getPage($page - 1);
                $request['max_id'] = $previousPage[count($previousPage) - 1]['id'];
            }
            $responseCode = $this->client->request('GET', $this->client->url('1.1/statuses/user_timeline'), $request);
            if ($responseCode == 200) {
                $this->pages[$page] = json_decode($this->client->response['response'], true);
            }
        }
        return $this->pages[$page];
    }
}