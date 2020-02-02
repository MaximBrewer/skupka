<?php

N2Loader::import('libraries.form.element.list');

class N2ElementZooCategories extends N2ElementList
{

    private $categories = array();

    function fetchElement() {
        $info     = $this->_form->get('info');
        $appid = $info->appid;

        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');

        $app = App::getInstance('zoo')->table->application->get($appid);

        $categories = $app->getCategories(true, null, true);


        $this->_xml->addChild('option', 'All')
                   ->addAttribute('value', 0);
        if (count($categories)) {
            foreach ($categories AS $category) {
                if (!isset($this->categories[$category->parent])) $this->categories[$category->parent] = array();
                $this->categories[$category->parent][] = $category;
            }
            $this->renderCategory(0, ' - ');
        }

        $html = parent::fetchElement();
        return $html;
    }

    function renderCategory($parent, $pre) {
        if (isset($this->categories[$parent])) {
            foreach ($this->categories[$parent] AS $category) {
                $this->_xml->addChild('option', htmlspecialchars($pre . $category->name))
                           ->addAttribute('value', $category->id);
                $this->renderCategory($category->id, $pre . ' - ');
            }
        }
    }

}
