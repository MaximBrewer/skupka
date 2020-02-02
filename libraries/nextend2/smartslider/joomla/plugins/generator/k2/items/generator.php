<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorK2Items extends N2GeneratorAbstract
{

    var $extraFields;

    function loadExtraFields() {
        static $extraFields = null;
        if ($extraFields === null) {

            $model = new N2Model('k2_extra_fields_groups');

            $query = 'SELECT ';
            $query .= 'groups.name AS group_name, ';
            $query .= 'field.name AS name, ';
            $query .= 'field.id ';

            $query .= 'FROM #__k2_extra_fields_groups AS groups ';

            $query .= 'LEFT JOIN #__k2_extra_fields AS field ON field.group = groups.id ';

            $query .= 'WHERE field.published = 1 ';

            $this->extraFields = $model->db->queryAll($query, false, "assoc", "id");
        }
    }

	public function datify($date, $format) {
        return date($format, strtotime($date));
    }
	
	public function removeSpecChar($str){
		return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $str);
	}
	
    protected function _getData($count, $startIndex) {
        $model = new N2Model('k2_items');

        $categories = array_map('intval', explode('||', $this->data->get('k2itemssourcecategories', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('k2itemssourcetags', '')));

        $query = 'SELECT ';
        $query .= 'con.id, ';
        $query .= 'con.title, ';
        $query .= 'con.alias, ';
        $query .= 'con.introtext, ';
        $query .= 'con.fulltext, ';
        $query .= 'con.catid, ';
		$query .= 'con.created, ';
        $query .= 'cat.name AS cat_title, ';
        $query .= 'cat.alias AS cat_alias, ';
        $query .= 'con.created_by, ';
        $query .= 'usr.name AS created_by_alias, ';
        $query .= 'con.hits, ';
        $query .= 'con.image_caption, ';
        $query .= 'con.image_credits, ';
        $query .= 'con.video, ';
        $query .= 'con.extra_fields ';

        $query .= 'FROM #__k2_items AS con ';

        $query .= 'LEFT JOIN #__users AS usr ON usr.id = con.created_by ';

        $query .= 'LEFT JOIN #__k2_categories AS cat ON cat.id = con.catid ';

        $jNow  = JFactory::getDate();
        $now   = $jNow->toSql();
        $where = array(
            "con.published = 1 AND (con.publish_up = '0000-00-00 00:00:00' OR con.publish_up < '" . $now . "') AND (con.publish_down = '0000-00-00 00:00:00' OR con.publish_down > '" . $now . "') ",
            'con.trash = 0 '
        );
        if (!in_array('0', $categories)) {
            $where[] = 'con.catid IN (' . implode(',', $categories) . ') ';
        }

        if (!in_array('0', $tags)) {
            $where[] = 'con.id IN ( SELECT itemID FROM #__k2_tags_xref WHERE tagID IN (' . implode(",", $tags) . ')) ';
        }

        $sourceUserId = intval($this->data->get('k2itemssourceuserid', ''));
        if ($sourceUserId) {
            $where[] = 'con.created_by = ' . $sourceUserId . ' ';
        }

        switch ($this->data->get('k2itemssourcefeatured', 0)) {
            case 1:
                $where[] = 'con.featured = 1 ';
                break;
            case -1:
                $where[] = 'con.featured = 0 ';
                break;
        }

        $language = $this->data->get('k2itemssourcelanguage', '*');
        if ($language) {
            $where[] = 'con.language = ' . $model->db->quote($language) . ' ';
        }

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = N2Parse::parse($this->data->get('k2itemsorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';
        $result = $model->db->queryAll($query);
        $this->loadExtraFields();

        require_once(JPATH_SITE . '/components/com_k2/helpers/utilities.php');
        if(!class_exists('K2ModelItem')){
            require_once(JPATH_ADMINISTRATOR.'/components/com_k2/models/model.php');
            require_once(JPATH_SITE . '/components/com_k2/models/item.php');
        }
        $k2item = new K2ModelItem();

        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $result[$i]['introtext'],
            );

            $thumbnail = JPATH_SITE . "/media/k2/items/cache/" . md5("Image" . $result[$i]['id']) . "_S.jpg";
            if (N2Filesystem::fileexists($thumbnail)) {
                $r['thumbnail'] = N2ImageHelper::dynamic(N2Uri::pathToUri($thumbnail));
            }

            $image = JPATH_SITE . "/media/k2/items/cache/" . md5("Image" . $result[$i]['id']) . "_XL.jpg";
            if (N2Filesystem::fileexists($image)) {
                $r['image'] = N2ImageHelper::dynamic(N2Uri::pathToUri($image));
            } else {
                $r['image'] = NextendImageFallBack::fallback($root . "/", array(), array($r['description']));
            }
            if (!isset($r['thumbnail'])) {
                $r['thumbnail'] = $r['image'];
            }
			
			$image = JPATH_SITE . "/media/k2/items/src/" . md5("Image" . $result[$i]['id']) . ".jpg";
            if (N2Filesystem::fileexists($image)) {
                $r['src_image'] = N2ImageHelper::dynamic(N2Uri::pathToUri($image));
            }

            if (!empty($result[$i]['video'])) {
                $r['video'] = $result[$i]['video'];
                preg_match_all('/(<source.*?src=[\'"](.*?)[\'"][^>]+>)/i', $result[$i]['video'], $video);
                $r['video_src'] = $video[2][0];
                preg_match_all('/(<source.*?src=[\'"](.*mp4)[\'"][^>]+>)/i', $result[$i]['video'], $mp4);
                if (isset($mp4[2][0])) {
                    $r['video_src_mp4'] = $mp4[2][0];
                }
            }

            $itemID = $this->data->get('k2itemsitemid', '0');
            $url    = 'index.php?option=com_k2&view=item&id=' . $result[$i]['id'] . ':' . $result[$i]['alias'];
            if (!empty($itemID) && $itemID != 0) {
                $url .= '&Itemid=' . $itemID;
            }

            $r += array(
                'url'              => $url,
                'url_label'        => sprintf(n2_('View %s'), n2_('item')),
                'category_title'   => $result[$i]['cat_title'],
                'category_url'     => 'index.php?option=com_k2&view=itemlist&task=category&id=' . $result[$i]['catid'] . ':' . $result[$i]['cat_alias'],
                'alias'            => $result[$i]['alias'],
                'id'               => $result[$i]['id'],
                'category_id'      => $result[$i]['catid'],
                'created_by_alias' => $result[$i]['created_by_alias'],
                'hits'             => $result[$i]['hits'],
                'image_caption'    => $result[$i]['image_caption'],
                'image_credits'    => $result[$i]['image_credits'],
                'created_date'     => $this->datify($result[$i]['created'], $this->data->get('sourcedateformat', 'Y-m-d')),
                'created_time'     => $this->datify($result[$i]['created'], $this->data->get('sourcetimeformat', 'G:i'))
            );

            $item   = (object)$result[$i];
            $extras = $k2item->getItemExtraFields($result[$i]['extra_fields'], $item);

			$count = 0;
            if (is_array($extras) && count($extras) > 0) {
                foreach ($extras AS $field) {
					$count++;
                    $r['extra' . $count] = $r['extra' . $this->removeSpecChar($field->id)] = $r['extra' . $this->removeSpecChar($field->id . '_' . preg_replace("/\W|_/", "", $this->extraFields[$field->id]['group_name'] . '_' . $this->extraFields[$field->id]['name']))] = $field->value;
                }
            }
            $data[] = $r;
        }
        return $data;
    }

}
