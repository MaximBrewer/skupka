<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorJEventsRepeatingEvents extends N2GeneratorAbstract
{

    private function formatDate($datetime, $dateOrTime, $format) {
        if ($dateOrTime == 1 || $datetime != '0000-00-00 00:00:00') {
            return date($format, strtotime($datetime));
        } else {
            return '0000-00-00';
        }
    }

    protected function _getData($count, $startIndex) {

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $calendars  = array_map('intval', explode('||', $this->data->get('sourcecalendars', '')));

        $dateFormat = $this->data->get('dateformat', 'Y-m-d');
        if (empty($dateFormat)) {
            $dateFormat = 'Y-m-d';
        }

        $timeFormat = $this->data->get('timeformat', 'H:i:s');
        if (empty($timeFormat)) {
            $timeFormat = 'H:i:s';
        }

        $itemId = $this->data->get('itemid', '0');
        $model  = new N2Model('jevents_vevent');

        $innerWhere = array();
        if (!in_array('0', $categories)) {
            $innerWhere[] = ' catid IN(' . implode(', ', $categories) . ')';
        }
        if (!in_array('0', $calendars)) {
            $innerWhere[] = ' icsid IN(' . implode(', ', $calendars) . ')';
        }

        if (!empty($innerWhere)) {
            $innerWhereStrAll = 'WHERE';
            $innerWhereStrAll .= implode(' AND ', $innerWhere);
        } else {
            $innerWhereStrAll = '';
        }

        $where    = array(
            "a.evdet_id IN (SELECT ev_id FROM #__jevents_vevent " . $innerWhereStrAll . ")",
            "a.evdet_id IN (SELECT eventid FROM #__jevents_repetition GROUP BY eventid HAVING COUNT(eventid) > 1)",
            "b.state = '1'"
        );
        $jevfiles = N2Filesystem::existsFile(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'jevents' . DIRECTORY_SEPARATOR . 'jevfiles' . DIRECTORY_SEPARATOR . 'jevfiles.php');
        $folder = '';
        if($jevfiles){
        		$params = JComponentHelper::getParams('com_media');
            $plugin = JPluginHelper::getPlugin('jevents', 'jevfiles');
            $params = new JRegistry($plugin->params);
            $folder.= rtrim(JURI::root(false),'/') . '/' . trim($params->get('image_path', 'images'),'/').'/'.trim($params->get('folder'),'/');
        }
        
        $today = time();

        switch ($this->data->get('noendtime', 0)) {
            case 1:
                $where[] = 'a.noendtime = 0';
                break;
            case -1:
                $where[] = 'a.noendtime = 1';
                break;
        }

        $location = $this->data->get('location', '*');
        if ($location != '*' && !empty($location)) {
            $where[] = "location = '" . $location . "'";
        }

        $order = N2Parse::parse($this->data->get('jeventsorder', 'a.dtstart|*|asc'));
        if ($order[0]) {
            $orderBy = 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query = 'SELECT d.rp_id, b.ev_id, FROM_UNIXTIME(a.dtstart) AS event_start,
                    FROM_UNIXTIME(a.dtend) AS event_end, a.description, a.location, a.summary,
                    a.contact, a.hits, a.extra_info ';

        if ($jevfiles) {
            $query .= ",(SELECT c.filename FROM #__jev_files AS c WHERE c.filetype = 'image' AND a.evdet_id = c.ev_id LIMIT 1) AS filename ";
        }

        $query .= ' FROM #__jevents_vevdetail AS a LEFT JOIN #__jevents_vevent
                    AS b ON a.evdet_id = b.detail_id ';

        $query .= 'LEFT JOIN #__jevents_repetition AS d ON a.evdet_id = d.eventid ';

        $query .= ' WHERE ' . implode(' AND ', $where) . ' GROUP BY b.ev_id ' . $orderBy . ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);
        $data   = array();

        $result = $model->db->queryAll($query);

        $data = array();
        foreach ($result AS $res) {
            $r     = array(
                'title'       => $res['summary'],
                'description' => $res['description']
            );

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($folder.'/', array(
                @$res['filename']
            ), array(
                $res['description']
            ));

            $r += array(
                'url'        => 'index.php?option=com_jevents&task=icalrepeat.detail&evid=' . $res['rp_id'] . '&Itemid=' . $itemId,
                'start_date' => $this->formatDate($res['event_start'], 0, $dateFormat),
                'start_time' => $this->formatDate($res['event_start'], 1, $timeFormat),
                'end_date'   => $this->formatDate($res['event_end'], 0, $dateFormat),
                'end_time'   => $this->formatDate($res['event_end'], 1, $timeFormat),
                'location'   => $res['location'],
                'contact'    => $res['contact'],
                'hits'       => $res['hits'],
                'extra_info' => $res['extra_info']
            );
            if ($jevfiles) {
                $r += array('filename' => $res['filename']);
            }
            $data[] = $r;
        }
        return $data;
    }
}
