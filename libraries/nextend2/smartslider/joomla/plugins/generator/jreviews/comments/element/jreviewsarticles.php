<?php
N2Loader::import('libraries.form.element.list');

class N2ElementJReviewsArticles extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        $query = 'SELECT asset_id, title FROM #__content WHERE catid IN (SELECT id FROM #__jreviews_categories WHERE `option` = \'com_content\' AND criteriaid IN
                      (SELECT id FROM #__jreviews_criteria WHERE state <> 0))';

        $db->setQuery($query);
        $articles = $db->loadObjectList();


        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', '0');
        if (count($articles)) {
            foreach ($articles AS $article) {
                $this->_xml->addChild('option', htmlspecialchars($article->title))
                           ->addAttribute('value', $article->asset_id);
            }
        }
        return parent::fetchElement();
    }

}
