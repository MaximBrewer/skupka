<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorEcwidProducts extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {


        $store = $this->info->getConfiguration()
                            ->getStoreID();

        $apiURL = 'http://app.ecwid.com/api/v1/' . $store . '/products';

        $categoryID = intval($this->data->get('categories', ''));
        if ($categoryID > 0) {
            $apiURL .= '?category=' . $categoryID;
        }

        $data = array();

        $json     = file_get_contents($apiURL);
        $products = json_decode($json);
        if (is_array($products)) {
            for ($i = 0; $i < count($products); $i++) {
                if (isset($products[$i]->name)) {
                    $data[$i]['title'] = $products[$i]->name;
                }

                if (isset($products[$i]->url)) {
                    $data[$i]['url'] = $products[$i]->url;
                }

                if (isset($products[$i]->description)) {
                    $data[$i]['description'] = $products[$i]->description;
                }

                if (isset($products[$i]->imageUrl)) {
                    $data[$i]['image'] = $products[$i]->imageUrl;
                }

                if (isset($products[$i]->thumbnailUrl)) {
                    $data[$i]['thumbnail'] = $products[$i]->thumbnailUrl;
                }

                if (isset($products[$i]->price)) {
                    $data[$i]['price'] = $products[$i]->price;
                }

                if (isset($products[$i]->sku)) {
                    $data[$i]['sku'] = $products[$i]->sku;
                }

                if (isset($products[$i]->quantity)) {
                    $data[$i]['quantity'] = $products[$i]->quantity;
                }

                if (isset($products[$i]->weight)) {
                    $data[$i]['weight'] = $products[$i]->weight;
                }
            }
        } else {
            return null;
        }
        return array_slice($data, $startIndex, $count);
    }

}
