<?php

class N2SliderGeneratorDribbbleConfiguration
{

    private $configuration;

    /**
     * @param $info N2GeneratorInfo
     */
    public function __construct($info) {
        $this->configuration = new N2Data(array(
            'apiKey'      => '',
            'apiSecret'   => '',
            'accessToken' => ''
        ));

        $this->configuration->loadJSON(N2Base::getApplication('smartslider')->storage->get('dribbble'));

    }

    public function wellConfigured() {
        if (!$this->configuration->get('apiKey') || !$this->configuration->get('apiSecret') || !$this->configuration->get('accessToken')) {
            return false;
        }

        $client = $this->getApi();
        try {

            $success = $client->CallAPI('https://api.dribbble.com/v1/user', 'GET', array(), array('FailOnAccessError' => true), $user);

            if (!$success) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getApi() {

        N2Loader::import('libraries.oauth.oauth');

        $client         = new N2OAuth();
        $client->server = 'Dribbble';

        $client->client_id     = $this->configuration->get('apiKey');
        $client->client_secret = $this->configuration->get('apiSecret');
        $client->access_token  = $this->configuration->get('accessToken', null);
        $client->scope         = '';
        $client->debug         = true;
        $client->debug_http    = true;

        return $client;
    }

    public function getData() {
        return $this->configuration->toArray();
    }

    public function addData($data, $store = true) {
        $this->configuration->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('dribbble', null, json_encode($this->configuration->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $form->loadXMLFile(dirname(__FILE__) . '/configuration.xml');

        echo $form->render('generator');

        try {
            $this->getApi();
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        $client               = $this->getApi();
        $client->redirect_uri = N2Base::getApplication('smartslider')->router->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => N2Request::getVar('group')
            )
        ));

        $client->Initialize();
        if (isset($_SESSION['OAUTH_ACCESS_TOKEN'])) unset($_SESSION['OAUTH_ACCESS_TOKEN']);
        if (isset($_SESSION['OAUTH_STATE'])) unset($_SESSION['OAUTH_STATE']);
        $client->access_token = '';
        $client->CheckAccessToken($redirect_uri);

        return $redirect_uri;
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $client               = $this->getApi();
            $client->redirect_uri = N2Base::getApplication('smartslider')->router->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => N2Request::getVar('group')
                )
            ));
            $client->Initialize();
            $client->CheckAccessToken($redirect_uri);
            $accessToken = $client->access_token;
            if (!$accessToken) {
                return false;
            } else {
                $data                = $this->getData();
                $data['accessToken'] = $accessToken;
                $this->addData($data);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getProjects() {
        $userID = N2Request::getVar('userID');
        if ($userID == '' || $userID == 'me') {

            $success = $this->getApi()
                            ->CallAPI('https://api.dribbble.com/v1/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        } else {
            $success = $this->getApi()
                            ->CallAPI('https://api.dribbble.com/v1/users/' . $userID . '/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        }

        $projects = array();
        if (count($result)) {
            foreach ($result AS $project) {
                $projects[$project->id] = $project->name;
            }
        }
        return $projects;
    }

}