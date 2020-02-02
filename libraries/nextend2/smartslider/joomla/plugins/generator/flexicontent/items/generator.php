<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorFlexiContentItems extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $model = new N2Model('#__content');

        $query = 'SELECT ';
        $query .= 'con.id, con.images ';

        $query .= 'FROM #__content AS con ';

        $query .= 'LEFT JOIN #__flexicontent_cats_item_relations AS fcat ON fcat.itemid = con.id ';
        $query .= 'LEFT JOIN #__categories AS cat ON fcat.catid = cat.id ';

        $where = array('con.state = 1 ');

        $category = array_map('intval', explode('||', $this->data->get('sourcecategory', '')));
        if (!in_array('0', $category)) {
            $where[] = 'fcat.catid IN (' . implode(',', $category) . ') ';
        }

        $tag = array_map('intval', explode('||', $this->data->get('sourcetag', '0')));
        if (!in_array('0', $tag)) {
            $where[] = ' con.id IN (SELECT itemid FROM #__flexicontent_tags_item_relations WHERE tid IN(' . implode(',', $tag) . '))';
        }

        $type = array_map('intval', explode('||', $this->data->get('sourcetype', '0')));
        if (!in_array('0', $type)) {
            $where[] = ' con.id IN (SELECT item_id FROM #__flexicontent_items_ext WHERE type_id IN(' . implode(',', $type) . '))';
        }

        switch ($this->data->get('sourcefeatured', 0)) {
            case 1:
                $where[] = 'con.featured = 1 ';
                break;
            case -1:
                $where[] = 'con.featured = 0 ';
                break;
        }

        $language = $this->data->get('sourcelanguage', '*');
        if ($language) {
            $db      = JFactory::getDbo();
            $where[] = 'con.language = ' . $db->quote($language) . ' ';
        }

        if (count($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $query .= 'GROUP BY con.id ';

        $order = N2Parse::parse($this->data->get('flexiorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $lng      = JFactory::getLanguage();
        $adminApp = JFactory::$application;
        $siteApp  = JApplicationCms::getInstance('site');
        $siteApp->loadLanguage($lng);


        require_once(JPATH_ADMINISTRATOR . DS . 'components/com_flexicontent/defineconstants.php');
        JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_flexicontent' . DS . 'tables');
        require_once(JPATH_SITE . DS . 'components' . DS . 'com_flexicontent' . DS . 'helpers' . DS . 'permission.php');
        require_once(JPATH_SITE . DS . "components/com_flexicontent/classes/flexicontent.fields.php");
        require_once(JPATH_SITE . DS . "components/com_flexicontent/classes/flexicontent.helper.php");
        require_once(JPATH_SITE . '/components/com_flexicontent/models/item.php');

        $app  = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid  = $user->getAuthorisedViewLevels();

        $itemModel = new FlexicontentModelItem();

        $root = JURI::root();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            JFactory::$application = $siteApp;
            $item                  = $itemModel->getItem($result[$i]['id'], $check_view_access = false);
            list($item) = FlexicontentFields::getFields($item, '', $item->parameters, $aid);
            JFactory::$application = $adminApp;

            $helper = array();
            foreach ($item->fields AS $k => $field) {
                if ($k == 'favourites' || $k == 'voting' || $k == 'state') {
                    continue;
                }
                $helper[$k] = FlexicontentFields::getFieldDisplay($item, $k, $values = null, $method = 'display');
            }

            $r = array(
                'title'       => $helper['title'],
                'description' => $helper['text']
            );

            if (!empty($result[$i]['images'])) {
                $img        = json_decode($result[$i]['images']);
                $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($root, array(
                    @$img->image_intro,
                    @$img->image_fulltext
                ), array(
                    $helper['text']
                ));
            }

            $r += array(
                'url'               => FlexicontentHelperRoute::getItemRoute($item->id, $item->catid),
                'url_label'         => n2_('View'),
                'creation_date'     => $helper['created'],
                'created_by'        => $helper['created_by'],
                'modification_date' => $helper['modified'],
                'modified_by'       => $helper['modified_by'],
                'hits'              => $helper['hits'],
                'document_type'     => $helper['document_type'],
                'version'           => $helper['version'],
                'categories'        => $helper['categories'],
                'tags'              => $helper['tags']
            );

            $data[] = $r;
        }
        JFactory::$application = $adminApp;
        return $data;
    }
}