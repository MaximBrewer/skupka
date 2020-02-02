<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorEasySocialVideos extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Videos');

        $where = array(
            "state = '1'"
        );

        $category = array_map('intval', explode('||', $this->data->get('easysocialcategories', '')));

        if (!in_array('0', $category)) {
            $where[] = 'category_id IN (' . implode(',', $category) . ')';
        }

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'featured = 1';
                break;
            case -1:
                $where[] = 'featured = 0';
                break;
        }

        $query = "SELECT * FROM #__social_videos WHERE " . implode(' AND ', $where) . "  ";

        $order = N2Parse::parse($this->data->get('easysocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'video'       => $result[$i]['path'],
                'title'       => $result[$i]['title'],
                'description' => $result[$i]['description'],
                'hits'        => $result[$i]['hits'],
                'thumbnail'   => !empty($result[$i]['thumbnail'])?'$/'.$result[$i]['thumbnail']:'',
                'id'          => $result[$i]['id']
            );

            $data[] = $r;
        }

        return $data;
    }
}
