<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorJomSocialGroups extends N2GeneratorAbstract
{

    private function checkImage($url, $image, $fallback = '') {
        if (!empty($image)) {
            return N2ImageHelper::dynamic($url . '/' . $image);
        } else if (!empty($fallback)) {
            return $fallback;
        } else {
            return '';
        }
    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . '/components/com_community/router.php');

        $model = new N2Model('community_groups');

        $where = array(
            "published = 1"
        );

        $category = array_map('intval', explode('||', $this->data->get('jomsocialgroupcategories', '0')));
        if (!in_array('0', $category)) {
            $where[] = 'categoryid IN (' . implode(',', $category) . ')';
        }

        switch ($this->data->get('featured', '-1')) {
            case 1:
                $where[] = "id IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
            case -1:
                $where[] = "id NOT IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
        }

        $grouptype = $this->data->get('grouptype', '-1');
        if ($grouptype != '-1') {
            $where[] = 'approvals = ' . $grouptype;
        }

        $query = "SELECT * FROM #__community_groups WHERE " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'       => $result[$i]['name'],
                'description' => $result[$i]['description'],
                'summary'     => $result[$i]['summary']
            );

            $r['image'] = NextendImageFallBack::fallback($root . "/", array(
                @$result[$i]['avatar'],
                @$result[$i]['cover']
            ), array(
                $r['description']
            ));

            $r['thumbnail'] = $this->checkImage($root, $result[$i]['thumb'], $r['image']);

            $r += array(
                'avatar'    => $this->checkImage($root, $result[$i]['avatar']),
                'cover'     => $this->checkImage($root, $result[$i]['cover']),
                'url'       => CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $result[$i]['id']),
                'url_label' => sprintf(n2_('View %s'), n2_('group')),
                'id'        => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
