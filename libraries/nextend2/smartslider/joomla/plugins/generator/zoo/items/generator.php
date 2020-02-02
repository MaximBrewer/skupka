<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorZooItems extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $data      = array();
        $appId     = $this->info->appid;
        $typeAlias = $this->info->identifier;

        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');
        $this->zoo = App::getInstance('zoo');

        $app   = $this->zoo->table->application->get($appId);
        $table = $this->zoo->table->item;

        $select = 'a.*';
        $from   = $table->name . ' AS a';

        $where = array();

        $where[] = 'a.application_id = ' . $appId;
        $where[] = "a.type = '" . $typeAlias . "'";
        $where[] = "a.state = 1";

        $now     = $this->zoo->date->create()
                                   ->toSQL();
        $null    = $this->zoo->database->getNullDate();
        $where[] = "(a.publish_up = '" . $null . "' OR a.publish_up < '" . $now . "')";
        $where[] = "(a.publish_down = '" . $null . "' OR a.publish_down > '" . $now . "')";

        $where[] = 'a.' . $this->zoo->user->getDBAccessString($this->zoo->user->get());

        $categories = array_map('intval', explode('||', $this->data->get('zooitemssourcecategories', '0')));
        if ($categories && !in_array(0, $categories)) {
            $from .= ' LEFT JOIN ' . ZOO_TABLE_CATEGORY_ITEM . ' AS ci ON a.id = ci.item_id';
            $where[] = 'ci.category_id IN (' . implode(',', $categories) . ') ';
        }

        $tags = explode('||', $this->data->get('zooitemssourcetags', 'All'));
        if (!empty($tags[0]) && !in_array('0', $tags)) {
            $where[] = 'a.id IN (SELECT item_id FROM #__zoo_tag WHERE name IN (' . implode(',', $tags) . ')) ';
        }

        $orderBy = '';
        $order   = N2Parse::parse($this->data->get('zooitemsorder', 'a.name|*|asc'));
        if ($order[0]) {
            $orderBy = $order[0] . ' ' . $order[1] . ' ';
        }

        $options = array(
            'select'     => $select,
            'from'       => $from,
            'conditions' => array(implode(' AND ', $where)),
            'order'      => $orderBy,
            'group'      => 'a.id',
            'offset'     => $startIndex,
            'limit'      => $count + $startIndex
        );

        $items = $table->all($options);
        $i     = 0;

        $types        = $app->getTypes();
        $skip = array('supercontact', 'linkpro', 'downloadpro');
        foreach ($items AS $item) {
            $typeElements = $types[$typeAlias]->getElements();
            $data[$i]          = array();
            $data[$i]['title'] = $item->name;
            $data[$i]['url']   = $this->zoo->route->item($item);
            $data[$i]['hits']  = $item->hits;

            $fields = array();            
            foreach ($typeElements AS $k => $el) {
                $type = $el->config->get('type');
                if(in_array($type, $skip)) continue;
                $el->setItem($item);
                $name     = str_replace('-', '', $type . '_' . $k);
                $fields[] = $data[$i][$name] = $el->render();
            }
            $data[$i]['image'] = $data[$i]['thumbnail'] = NextendImageFallBack::fallback('', array(), $fields);

            $i++;
        }
        return $data;
    }

}
