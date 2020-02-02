<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorEasySocialAlbums extends N2GeneratorAbstract {

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Albums');

        $groups = array_map('intval', explode('||', $this->data->get('easysocialgroups', '0')));
        $events = array_map('intval', explode('||', $this->data->get('easysocialevents', '0')));

        if (!in_array('0', $groups) && !in_array('0', $events)) {
            $clusters = array_merge($groups, $events);
        } else if (!in_array('0', $groups)) {
            $clusters = $groups;
        } else if (!in_array('0', $events)) {
            $clusters = $events;
        } else {
            $clusters = '';
        }

        if (in_array('0', $groups) && in_array('0', $events)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group' OR cluster_type = 'event')";
        } else if (in_array('0', $groups)) {
            $all = '';
        } else if (in_array('0', $events)) {
            $all = "OR uid IN (SELECT cluster_id FROM #__social_events_meta WHERE group_id IN (" . implode(',', $groups) . "))";
        } else {
            $all = "OR uid IN (SELECT cluster_id FROM #__social_events_meta WHERE group_id IN (" . implode(',', $groups) . ") AND cluster_id IN(" . implode(',', $events) . "))";
        }

        $albumWhere = array( "(type='event' OR type='group')" );

        if ($clusters != '') {
            $albumWhere[] = "(uid IN (" . implode(',', $clusters) . ") " . $all . ")";
        }

        if ($this->data->get('avatarandcover', '0') == '0') {
            $albumWhere[] = "title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR' AND title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER'";
        }

        $albumTitle = $this->data->get('albumtitle', '*');
        if ($albumTitle != '*' && !empty($albumTitle)) {
            $albumWhere[] = "title = '" . $albumTitle . "'";
        }

        $where = array(
            "album_id IN (SELECT id FROM #__social_albums WHERE  " . implode(' AND ', $albumWhere) . ")",
            "state = 1"
        );

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'featured = 1';
                break;
            case -1:
                $where[] = 'featured = 0';
                break;
        }

        $query = "SELECT
                  id, title
                  FROM #__social_photos
                  WHERE " . implode(' AND ', $where);

        $order = N2Parse::parse($this->data->get('easysocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= " LIMIT " . $startIndex . ", " . $count;

        $result = $model->db->queryAll($query);
        $data   = array();

        // EasySocial quote: "Prior to ES 2.0, we no longer use square and featured as image variation". This is why the photos are returning thumbnail and large images.
        $photo = ES::table('Photo');
        for ($i = 0; $i < count($result); $i++) {
            $photo->load($result[$i]['id']);
            $r = array(
                'title'     => $result[$i]['title'],
                'image'     => $photo->getSource('original'),
                'thumbnail' => $photo->getSource('thumbnail'),
                'square'    => $photo->getSource('square'),
                'featured'  => $photo->getSource('featured'),
                'large'     => $photo->getSource('large'),
                'stock'     => $photo->getSource('stock')
            );

            $data[] = $r;
        }

        return $data;
    }
}
