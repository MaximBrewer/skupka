<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorODudeECard extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $model = new N2Model("ecard_media");

        $categories = array_map('intval', explode('||', $this->data->get('odudecategories', '')));

        $where = array(
            "type = '" . $this->data->get('odudetypes', 'J') . "'",
            'published = 1'
        );

        if (!in_array(0, $categories)) {
            $where[] = 'cat IN (' . implode(',', $categories) . ')';
        }

        $order = N2Parse::parse($this->data->get('odudeorder', 'ddate|*|desc'));
        if ($order[0]) {
            $orderBy = 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query = 'SELECT * FROM #__ecard_media WHERE ' . implode(' AND ', $where) . ' ' . $orderBy . ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = JURI::root();
        $data = array();
        foreach ($result AS $card) {
            $r      = array(
                'image'            => N2ImageHelper::dynamic($root . 'media/ecard/' . $card['file']),
                'thumbnail'        => N2ImageHelper::dynamic($root . 'media/ecard/' . $card['thumb']),
                'title'            => $card['title'],
                'description'      => $card['code'],
                'meta_description' => $card['desp'],
                'url'              => JRoute::_('index.php?option=com_odudecard&id=' . $card['id'] . '&controller=odudecardshow&cate=' . $card['cat']),
                'url_label'        => sprintf(n2_('View %s'), n2_('E-card')),
                'category_url'     => JRoute::_('index.php?option=com_odudecard&controller=odudecardlist&cate=' . $card['cat']),
                'hits'             => $card['hits'],
                'file'             => $card['file'],
                'point'            => $card['point'],
                'created_by'       => $card['username'],
                'creation_date'    => $card['ddate']
            );
            $data[] = $r;
        }
        return $data;
    }
}