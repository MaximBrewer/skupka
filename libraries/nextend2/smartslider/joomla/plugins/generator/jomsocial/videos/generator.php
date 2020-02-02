<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorJomSocialVideos extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . '/components/com_community/router.php');

        $model = new N2Model('community_videos');

        $where = array(
            "published = 1",
            "type = '" . $this->data->get('videotype', 'youtube') . "'"
        );

        $group  = array_map('intval', explode('||', $this->data->get('jomsocialgroups', '0')));
        $events = array_map('intval', explode('||', $this->data->get('jomsocialevents', '0')));

        if (!in_array('0', $group) && !in_array('0', $events)) {
            $where[] = '(groupid IN (' . implode(',', $group) . ') OR eventid IN (' . implode(',', $events) . '))';
        } else if (!in_array('0', $group)) {
            $where[] = 'groupid IN (' . implode(',', $group) . ')';
        } else if (!in_array('0', $events)) {
            $where[] = 'eventid IN (' . implode(',', $events) . ')';
        }

        $userID = $this->data->get('userid', '*');
        if ($userID != '*' && !empty($userID)) {
            $where[] = 'creator IN (' . $userID . ')';
        }

        switch ($this->data->get('featured', '-1')) {
            case 1:
                $where[] = "featured = 1";
                break;
            case -1:
                $where[] = "featured = 0";
                break;
        }

        $query = "SELECT * FROM #__community_videos WHERE " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $image  = N2ImageHelper::dynamic($root . '/' . $result[$i]['thumb']);
            $r      = array(
                'title'      => $result[$i]['title'],
                'video_path' => $result[$i]['path'],
                'video_id'   => $result[$i]['video_id'],
                'image'      => $image,
                'thumbnail'  => $image,
                'id'         => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
