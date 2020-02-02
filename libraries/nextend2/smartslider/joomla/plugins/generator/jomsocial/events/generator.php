<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorJomSocialEvents extends N2GeneratorAbstract
{

    private function formatDate($datetime, $dateOrTime = 0) {
        switch ($dateOrTime) {
            case 0:
                $dot = 'Y-m-d';
                break;
            case 1:
                $dot = 'H:i';
                break;
        }
        return date($dot, strtotime($datetime));
    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . '/components/com_community/router.php');

        $model = new N2Model('community_events');

        $where = array(
            "published = 1"
        );

        $category = array_map('intval', explode('||', $this->data->get('jomsocialcategories', '0')));
        if (!in_array('0', $category)) {
            $where[] = 'catid IN (' . implode(',', $category) . ')';
        }

        $group = array_map('intval', explode('||', $this->data->get('jomsocialgroups', '0')));
        if (!in_array('0', $group)) {
            $where[] = 'contentid IN (' . implode(',', $group) . ')';
        }

        $profiles = array_map('intval', explode('||', $this->data->get('jomsocialprofiles', '0')));
        if (!in_array('0', $profiles)) {
            $where[] = 'creator IN (SELECT userid FROM #__community_users WHERE profile_id IN (' . implode(',', $profiles) . '))';
        }

        $today = date('Y-m-d h:i:s', time());

        switch ($this->data->get('started', '0')) {
            case 1:
                $where[] = "startdate < '" . $today . "'";
                break;
            case -1:
                $where[] = "startdate >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $where[] = "enddate < '" . $today . "'";
                break;
            case -1:
                $where[] = "enddate >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('featuredevents', '-1')) {
            case 1:
                $where[] = "id IN (SELECT cid FROM #__community_featured WHERE type = 'events')";
                break;
            case -1:
                $where[] = "id NOT IN (SELECT cid FROM #__community_featured WHERE type = 'events')";
                break;
        }

        switch ($this->data->get('featuredusers', '-1')) {
            case 1:
                $where[] = "creator IN (SELECT cid FROM #__community_featured WHERE type = 'users')";
                break;
            case -1:
                $where[] = "creator NOT IN (SELECT cid FROM #__community_featured WHERE type = 'users')";
                break;
        }

        switch ($this->data->get('featuredgroups', '-1')) {
            case 1:
                $where[] = "contentid IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
            case -1:
                $where[] = "contentid NOT IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
        }

        switch ($this->data->get('invitation', '-1')) {
            case 1:
                $where[] = "permission = 1";
                break;
            case -1:
                $where[] = "permission = 0";
                break;
        }

        switch ($this->data->get('hidden', '-1')) {
            case 1:
                $where[] = "unlisted = 1";
                break;
            case -1:
                $where[] = "unlisted = 0";
                break;
        }

        $location = $this->data->get('location', '*');
        if ($location != '*' && !empty($location)) {
            $where[] = "location = '" . $location . "'";
        }

        $userid = $this->data->get('userid', '*');
        if ($userid != '*' && !empty($userid)) {
            $where[] = "creator IN (" . $userid . ")";
        }

        $query = "SELECT * FROM #__community_events WHERE " . implode(' AND ', $where) . "  ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'startdate|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'             => $result[$i]['title'],
                'short_description' => $result[$i]['summary'],
                'description'       => $result[$i]['description']
            );

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($root . "/", array(
                @$result[$i]['cover']
            ), array(
                $r['description']
            ));

            $r += array(
                'url'        => CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid=' . $result[$i]['id']),
                'start_date' => $this->formatDate($result[$i]['startdate']),
                'start_time' => $this->formatDate($result[$i]['startdate'], 1),
                'end_date'   => $this->formatDate($result[$i]['enddate']),
                'end_time'   => $this->formatDate($result[$i]['enddate'], 1),
                'location'   => $result[$i]['location'],
                'latitude'   => $result[$i]['latitude'],
                'longitude'  => $result[$i]['longitude'],
                'id'         => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
