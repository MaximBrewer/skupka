<?php

N2Loader::import('libraries.form.element.list');

class N2ElementYoutubePlaylistByUser extends N2ElementList
{

    function fetchElement() {
        try {
            /** @var N2GeneratorInfo $info */
            $info          = $this->_form->get('info');
            $client        = $info->getConfiguration()->getApi();
            $youtubeClient = new Google_Service_YouTube($client);
            $playlists     = $youtubeClient->playlists->listPlaylists('id,snippet', array('mine' => true, 'maxResults' => 50));

            foreach ($playlists['items'] AS $k => $item) {
                $this->_xml->addChild('option', htmlentities($item['snippet']['title']))->addAttribute('value', $item['id']);
            }

            if (isset($playlists['items'][0])) {
                $this->_default = $playlists['items'][0]['id'];
            }

        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        return parent::fetchElement();
    }

}
