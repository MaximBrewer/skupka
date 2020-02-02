<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorEasySocialEvents extends N2GeneratorAbstract {

    private function formatDate($datetime, $dateOrTime = 0) {
        switch ($dateOrTime) {
            case 0:
                $dot = 'Y-m-d';
                break;
            case 1:
                $dot = 'H:i:s';
                break;
        }
        if ($dateOrTime == 1 || $datetime != '0000-00-00 00:00:00') {
            return date($dot, strtotime($datetime));
        } else {
            return '0000-00-00';
        }
    }

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Events');

        $where = array(
            "a.cluster_type = 'event'",
            "a.state = '1'"
        );

        $category = array_map('intval', explode('||', $this->data->get('easysocialcategories', '')));

        if (!in_array('0', $category)) {
            $where[] = 'a.category_id IN (' . implode(',', $category) . ')';
        }

        $today = date('Y-m-d h:i:s', time());

        switch ($this->data->get('started', '0')) {
            case 1:
                $where[] = "b.start < '" . $today . "'";
                break;
            case -1:
                $where[] = "b.start >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $where[] = "(b.end < '" . $today . "' AND b.end <> '0000-00-00 00:00:00')";
                break;
            case -1:
                $where[] = "(b.end >= '" . $today . "' OR b.end = '0000-00-00 00:00:00')";
                break;
        }

        switch ($this->data->get('allday', 0)) {
            case 1:
                $where[] = 'b.all_day = 1';
                break;
            case -1:
                $where[] = 'b.all_day = 0';
                break;
        }
		
        switch ($this->data->get('recurring', 0)) {
            case 0:
                $groupby = 'GROUP BY a.id ';
                break;
            case 1:
                $where[] = 'a.parent_id <> 0';
                $groupby = 'GROUP BY a.parent_id ';
                break;
            case -1:
                $where[] = 'a.parent_id = 0';
                $groupby = 'GROUP BY a.id ';
                break;
        }

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'a.featured = 1';
                break;
            case -1:
                $where[] = 'a.featured = 0';
                break;
        }

        $type = $this->data->get('eventtype', 0);
        if ($type != 0) {
            $where[] = 'a.type = ' . $type;
        }

        $location = $this->data->get('location', '*');
        if ($location != '*' && !empty($location)) {
            $where[] = "a.address = '" . $location . "'";
        }

        $query = "SELECT
                  a.title, a.description, a.address, a.longitude, a.latitude, a.created, a.alias, a.category_id, a.id, a.alias,
                  b.start, b.end,
                  c.small, c.medium, c.square, c.large, c.uid,
                  (SELECT photo_id FROM #__social_covers WHERE uid = a.id and type='event' LIMIT 1) AS photo_id
                  FROM #__social_clusters AS a
                  LEFT JOIN #__social_events_meta AS b ON b.cluster_id = a.id
                  LEFT JOIN #__social_avatars AS c ON c.uid = a.id
                  WHERE " . implode(' AND ', $where) . "  ";

        $query .= $groupby;

        $order = N2Parse::parse($this->data->get('easysocialorder', 'b.start|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result     = $model->db->queryAll($query);
        $root       = N2Uri::getBaseUri();

        if (!class_exists('FRoute')) {
            $file = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'easysocial.php';
            if (file_exists($file)) {
                require_once($file);
            }
            require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'router.php');
        }

        $urlOptions = array(
            'layout'   => 'item',
            'external' => false,
            'sef'      => true
        );

        $avatar = ES::table('Avatar');
        $photo  = ES::table('Photo');

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $urlOptions['id'] = $result[$i]['id'];
            $photo->load($result[$i]['photo_id']);
            $avatar->load(array( 'uid' => $result[$i]['uid'], 'type' => 'event' ));
            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $result[$i]['description']
            );

            $r['thumbnail'] = $photo->getSource('thumbnail');
            $r['image']     = NextendImageFallBack::fallback($root, array(
                $photo->getSource('original'),
                $photo->getSource('large')
            ), array());

            $thumbnail = '';
            if ($r['thumbnail'] == '' && $r['image'] != '') {
                $thumbnail      = $photo->getSource('thumbnail');
                $r['thumbnail'] = !empty($thumbnail) ? $thumbnail : $r['image'];
            }
            // EasySocial quote: "Prior to ES 2.0, we no longer use square and featured as image variation". This is why the photos are returning thumbnail and large images.
            $r += array(
                'square_image'        => $photo->getSource('square'),
                'featured_image'      => $photo->getSource('featured'),
                'large_image'         => $photo->getSource('large'),
                'stock_image'         => $photo->getSource('stock'),
                'avatar_small_image'  => $avatar->getSource('small'),
                'avatar_medium_image' => $avatar->getSource('medium'),
                'avatar_square_image' => $avatar->getSource('square'),
                'avatar_large_image'  => $avatar->getSource('large'),
                'url'                 => FRoute::events($urlOptions, true),
                'start_date'          => $this->formatDate($result[$i]['start']),
                'start_time'          => $this->formatDate($result[$i]['start'], 1),
                'end_date'            => $this->formatDate($result[$i]['end']),
                'end_time'            => $this->formatDate($result[$i]['end'], 1),
                'address'             => $result[$i]['address'],
                'longitude'           => $result[$i]['longitude'],
                'latitude'            => $result[$i]['latitude'],
                'creation_time'       => $result[$i]['created'],
                'alias'               => $result[$i]['alias'],
                'category_id'         => $result[$i]['category_id'],
                'id'                  => $result[$i]['id']
            );

            $data[] = $r;
        }

        return $data;
    }
}