<?php

N2Loader::import('libraries.form.element.list');
N2Loader::import('libraries.parse.parse');

class N2ElementDribbbleProjects extends N2ElementList
{

    function fetchElement() {

        N2JS::addInline('
            new N2Classes.FormElementDribbbleProjects("' . $this->_id . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getData",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '");
        ');

        /** @var N2GeneratorInfo $info */
        $info   = $this->_form->get('info');
        $client = $info->getConfiguration()
                       ->getApi();

        try {
            $userID = $this->_form->get('dribbble-user-id', 'me');
            $result = null;
            if ($userID == 'me') {
                $success = $client->CallAPI('https://api.dribbble.com/v1/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            } else {
                $success = $client->CallAPI('https://api.dribbble.com/v1/users/' . $userID . '/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            }
            if (count($result)) {
                foreach ($result AS $project) {
                    $this->_xml->addChild('option', htmlentities($project->name))
                               ->addAttribute('value', $project->id);
                }
                if ($this->getValue() == '') {
                    $this->setValue($result[0]->id);
                }
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return parent::fetchElement();
    }

}
