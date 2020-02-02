<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorRedShopProducts extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
        require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/configuration.php';
        require_once JPATH_SITE . '/components/com_redshop/helpers/product.php';
        require_once JPATH_SITE . '/components/com_redshop/helpers/user.php';

        $where = array(' pr.published = 1 ');

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'pr_cat.category_id IN (' . implode(',', $categories) . ') ';
        }

        $manufacturers = array_map('intval', explode('||', $this->data->get('sourcemanufacturers', '')));
        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'pr.manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
        }

        $suppliers = array_map('intval', explode('||', $this->data->get('sourcesuppliers', '')));
        if (!in_array(0, $suppliers) && count($suppliers) > 0) {
            $where[] = 'pr.supplier_id IN (' . implode(',', $suppliers) . ') ';
        }

        switch ($this->data->get('sourcefeatured', 0)) {
            case 1:
                $where[] = ' pr.product_special = 1 ';
                break;
            case -1:
                $where[] = ' pr.product_special = 0 ';
                break;
        }

        switch ($this->data->get('sourceonsale', 0)) {
            case 1:
                $where[] = ' pr.product_on_sale = 1 ';
                break;
            case -1:
                $where[] = ' pr.product_on_sale = 0 ';
                break;
        }

        switch ($this->data->get('sourceexpired', 0)) {
            case 1:
                $where[] = ' pr.expired = 1 ';
                break;
            case -1:
                $where[] = ' pr.expired = 0 ';
                break;
        }

        switch ($this->data->get('sourceforsell', 0)) {
            case 1:
                $where[] = ' pr.not_for_sale = 1 ';
                break;
            case -1:
                $where[] = ' pr.not_for_sale = 0 ';
                break;
        }

        $parentID = $this->data->get('product_parent_id', '*');
        if (is_numeric($parentID)) {
            $where[] = ' pr.product_parent_id = ' . $parentID . ' ';
        }

        $o = '';

        $order = N2Parse::parse($this->data->get('redshopproductsorder', 'pr.product_name|*|asc'));
        if ($order[0]) {
            $o .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $model = new N2Model('redshop_product');

        $query = "SELECT
                        pr.product_id, 
                        pr.published, 
                        pr_cat.ordering, 
                        pr.product_name as name, 
                        pr.product_s_desc as short_description, 
                        pr.product_desc as description,
                        man.manufacturer_name as man_name,
                        pr.product_full_image as image, 
                        pr.product_thumb_image as image_thumbnail, 
                        pr.product_price,
                        pr.discount_price,
                        pr.visited,
                        pr.weight,
                        pr.product_length,
                        pr.product_height,
                        pr.product_width,
                        pr.product_diameter,
                        pr.product_preview_image,
                        pr.product_preview_back_image,
                        cat.category_id,
                        cat.category_name, 
                        cat.category_short_description , 
                        cat.category_description
                    FROM `#__redshop_product` AS pr
                    LEFT JOIN `#__redshop_product_category_xref` AS pr_cat USING (product_id)
                    LEFT JOIN `#__redshop_category` AS cat USING (category_id)
                    LEFT JOIN `#__redshop_manufacturer` AS man USING(manufacturer_id)
                    WHERE " . implode(' AND ', $where) . " GROUP BY pr.product_id " . $o . " LIMIT " . $startIndex . ", " . $count;

        $result = $model->db->queryAll($query);

        $product = new producthelper;
        //Redconfiguration needed for REDSHOP_FRONT_IMAGES_ABSPATH
        new Redconfiguration;
        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {

            $r = array(
                'title'             => $result[$i]['name'],
                'url'               => 'index.php?option=com_redshop&view=product&pid=' . $result[$i]['product_id'] . '&cid=' . $result[$i]['category_id'],
                'description'       => $result[$i]['description'],
                'short_description' => $result[$i]['short_description'],
            );

            $r['image'] = NextendImageFallBack::fallback(REDSHOP_FRONT_IMAGES_ABSPATH . "product/", array(
                @$result[$i]['image'],
                @$result[$i]['product_preview_image'],
                @$result[$i]['image_thumbnail']
            ));
            
            if (empty($r['image'])) {
                $r['image'] = NextendImageFallBack::fallback($root . "/", array(), array(
                    $r['description'],
                    $r['short_description']
                ));
            }

            if (!empty($result[$i]['image_thumbnail'])) {
                $r['thumbnail'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['image_thumbnail']);
            } else if (!empty($result[$i]['image'])) {
                $r['thumbnail'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['image']);
            }

            $r['price'] = $product->getProductFormattedPrice($result[$i]['product_price']);

            if (!empty($result[$i]['product_preview_image'])) {
                $r['product_preview_image'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['product_preview_image']);
            }
            if (!empty($result[$i]['product_preview_back_image'])) {
                $r['product_preview_back_image'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['product_preview_back_image']);
            }

            $r += array(
                'unformatted_price'          => $result[$i]['product_price'],
                'discount_price'             => $result[$i]['discount_price'],
                'category_name'              => $result[$i]['category_name'],
                'category_url'               => 'index.php?option=com_redshop&view=category&cid=' . $result[$i]['category_id'] . '&layout=detail',
                'category_description'       => $result[$i]['category_description'],
                'category_short_description' => $result[$i]['category_short_description'],
                'manufacturer_name'          => $result[$i]['man_name'],
                'hits'                       => $result[$i]['visited'],
                'weight'                     => $result[$i]['weight'],
                'product_length'             => $result[$i]['product_length'],
                'product_height'             => $result[$i]['product_height'],
                'product_width'              => $result[$i]['product_width'],
                'product_diameter'           => $result[$i]['product_diameter'],
                'id'                         => $result[$i]['product_id']
            );

            $data[] = $r;
        }
        return $data;
    }

}
