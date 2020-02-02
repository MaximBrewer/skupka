<?php

class N2SliderGeneratorFlickrConfiguration
{

    private $configuration;

    /**
     * @param $info N2GeneratorInfo
     */
    public function __construct() {
        $this->configuration = new N2Data(array(
            'apikey'    => '',
            'apisecret' => '',
            'token'     => ''
        ));

        $this->configuration->loadJSON(N2Base::getApplication('smartslider')->storage->get('flickr'));
    }

    public function wellConfigured() {
        if (!$this->configuration->get('api_key') || !$this->configuration->get('api_secret') || !$this->configuration->get('token')) {
            return false;
        }
        $api = $this->getApi();
        if ($api->test_login() === false) {
            return false;
        }
        return true;
    }

    public function getApi() {
        require_once(dirname(__FILE__) . "/api/phpFlickr.php");
        $api_key    = $this->configuration->get('api_key');
        $api_secret = $this->configuration->get('api_secret');
        
        $api = new phpFlickr($api_key, $api_secret);
        $api->setToken($this->configuration->get('token'));
        return $api;
    }

    public function getData() {
        return $this->configuration->toArray();
    }

    public function addData($data, $store = true) {
        $this->configuration->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('flickr', null, json_encode($this->configuration->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $form->loadXMLFile(dirname(__FILE__) . '/configuration.xml');
        $api = $this->getApi();
        if ($api->test_login() === false) {
            N2Message::error(n2_('The key and secret is not valid!'));
        }

        echo $form->render('generator');
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        return $this->getApi()
                    ->get_auth_url("read");
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $api           = $this->getApi();
            $result        = $api->auth_getToken($_GET['frob']);
            $data          = $this->getData();
            $data['token'] = $result['token'];
            $this->addData($data);
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }

}
