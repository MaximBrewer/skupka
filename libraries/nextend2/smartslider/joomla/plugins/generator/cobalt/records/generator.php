<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorCobaltRecords extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $model = new N2Model("js_res_record");

        $category = array_map('intval', explode('||', $this->data->get('cobaltrecordssourcecategory', '')));

        $query = 'SELECT ';

        $query .= 'rec.id ';

        $query .= 'FROM #__js_res_record AS rec ';

        $query .= 'LEFT JOIN #__js_res_record_category AS cat ON cat.record_id = rec.id ';

        $where = array('rec.section_id = \'' . $this->info->section_id . '\' ');

        if (!in_array(0, $category) && count($category) > 0) {
            $where[] = 'cat.catid IN (' . implode(',', $category) . ') ';
        }

        $type    = intval($this->data->get('cobaltrecordssourcetype', 0));
        $where[] = 'rec.type_id = \'' . $type . '\' ';


        $sourceuserid = intval($this->data->get('cobaltrecordssourceuserid', ''));
        if ($sourceuserid) {
            $where[] = ' rec.user_id = ' . $sourceuserid . ' ';
        }

        switch ($this->data->get('cobaltrecordssourcepublished', 1)) {
            case 1:
                $where[] = ' rec.published = 1 ';
                break;
            case -1:
                $where[] = ' rec.published = 0 ';
                break;
        }

        switch ($this->data->get('cobaltrecordssourcefeatured', 0)) {
            case 1:
                $where[] = ' rec.featured = 1 ';
                break;
            case -1:
                $where[] = ' rec.featured = 0 ';
                break;
        }

        $language = $this->data->get('cobaltrecordssourcelanguage', '*');
        if ($language && $language != '*') {
            $where[] = ' rec.langs = ' . $model->quote($language) . ' ';
        }
        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = N2Parse::parse($this->data->get('cobaltrecordsorder', 'rec.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $ids = $model->db->queryAll($query);
        require_once JPATH_SITE . '/components/com_cobalt/models/form.php';
        if (version_compare(JVERSION, '2.5.6', 'lt')) {
            jimport('joomla.application.component.model');
        } else {
            jimport('joomla.application.component.modellegacy');
        }
        JLoader::import('record', JPATH_SITE . '/components/com_cobalt/models');

        $model = null;
        if (version_compare(JVERSION, '2.5.6', 'lt')) {
            $model = JModel::getInstance('record', 'CobaltModel');
        } else {
            $model = JModelLegacy::getInstance('record', 'CobaltModel');
        }

        $sectionModel = null;
        if (version_compare(JVERSION, '2.5.6', 'lt')) {
            $sectionModel = JModel::getInstance('section', 'CobaltModel');
        } else {
            $sectionModel = JModelLegacy::getInstance('section', 'CobaltModel');
        }

        $categoryModel = null;
        if (version_compare(JVERSION, '2.5.6', 'lt')) {
            $categoryModel = JModel::getInstance('category', 'CobaltModel');
        } else {
            $categoryModel = JModelLegacy::getInstance('category', 'CobaltModel');
        }

        $data = array();
        foreach ($ids AS $id) {
            $modelItem = $model->getItem($id['id']);
            JFactory::getApplication()->input->set('id', $modelItem->id);
            $rec      = $model->_prepareItem($modelItem);
			
            $user     = JFactory::getUser($rec->user_id);
            $section  = $sectionModel->getItem($rec->section_id);
            $category = $categoryModel->getItem($rec->section_id);
            $r        = array(
                'title'            => $rec->title,
                'url'              => $rec->url,
                'url_label'        => sprintf(n2_('View %s'), n2_('record')),
                'hits'             => $rec->hits,
                'created_by_alias' => $user->get('name'),
                'section_title'    => $section->name,
                'section_url'      => Url::records($section),
                'category_title'   => $category->title,
                'category_url'     => Url::records($section, $category),
                'type_title'       => $rec->type_name,
                'id'               => $rec->id,
                'created_by_id'    => $rec->user_id,
                'section_id'       => $rec->section_id,
                'category_id'      => $rec->category_id,
                'type_id'          => $rec->type_id
            );
            if (empty($r['created_by_alias'])) {
                $r['created_by_alias'] = 'Guest';
            }
            if (is_array($rec->fields_by_id) && count($rec->fields_by_id) > 0) {
                $fields = array();
                foreach ($rec->fields_by_id AS $id => $field) {
                    $r_name   = 'extra' . $id . '_' . preg_replace("/\W|_/", "", $field->getLabelName());
                    $fields[] = $r[$r_name] = $field->result;
                }
                $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback(N2Uri::getBaseUri(), array(), $fields);
            }
			if(is_array($rec->fields) && count($rec->fields) > 0){
				foreach ($rec->fields AS $key => $value) {
					if(is_array($value) && count($value) > 0){
						foreach ($value AS $vkey => $vvalue) {
							if(is_array($vvalue) && count($vvalue) > 0){
								foreach ($vvalue AS $vvkey => $vvvalue) {
									$r['field_'.$key.'_'.$vkey.'_'.$vvkey] = $vvvalue;
								}
							} else {
								$r['field_'.$key.'_'.$vkey] = $vvalue;
							}
						}
					} else {
						$r['field_'.$key] = $value;
					}
				}
			}
            $data[] = $r;
        }
        return $data;
    }
}