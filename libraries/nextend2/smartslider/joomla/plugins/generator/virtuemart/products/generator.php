<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorVirtueMartProducts extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        VmConfig::loadConfig();

        $language = $this->data->get('virtuemartlanguage', 'en_gb');
        if (!$language) $language = VMLANG;

        $categories    = array_map('intval', explode('||', $this->data->get('virtuemartcategories', '')));
        $manufacturers = array_map('intval', explode('||', $this->data->get('virtuemartmanufacturers', '')));

        $model = new N2Model('virtuemart_products');

        $query = 'SELECT ';
        $query .= 'prod.virtuemart_product_id AS id, ';
        $query .= 'prod.product_sku AS sku, ';
        $query .= 'prod_ext.product_name AS name, ';
        $query .= 'prod_ext.product_s_desc AS short_description, ';
        $query .= 'prod_ext.product_desc AS description, ';
        $query .= 'prod_ext.slug AS slug, ';

        $query .= 'cat.virtuemart_category_id AS category_id, ';
        $query .= 'cat.category_name, ';
        $query .= 'cat.category_description, ';
        $query .= 'cat.slug AS category_slug, ';

        $query .= 'man.virtuemart_manufacturer_id AS manufacturer_id, ';
        $query .= 'man.mf_name AS manufacturer_name, ';
        $query .= 'man.mf_email AS manufacturer_email, ';
        $query .= 'man.mf_desc AS manufacturer_description, ';
        $query .= 'man.mf_url AS manufacturer_url, ';
        $query .= 'man.slug AS manufacturer_slug, ';

        $query .= 'med.file_url AS image, ';
        $query .= 'med.file_url_thumb AS thumbnail ';

        $query .= 'FROM #__virtuemart_products AS prod ';

        $query .= 'LEFT JOIN #__virtuemart_products_' . $language . ' AS prod_ext ON prod.virtuemart_product_id = prod_ext.virtuemart_product_id ';

        $query .= 'LEFT JOIN #__virtuemart_product_categories AS cat_x ON cat_x.virtuemart_product_id = prod.virtuemart_product_id ';

        $query .= 'LEFT JOIN #__virtuemart_categories_' . $language . ' AS cat ON cat_x.virtuemart_category_id = cat.virtuemart_category_id ';

        $query .= 'LEFT JOIN #__virtuemart_product_manufacturers AS man_x ON man_x.virtuemart_product_id = prod.virtuemart_product_id ';

        $query .= 'LEFT JOIN #__virtuemart_manufacturers_' . $language . ' AS man ON man_x.virtuemart_manufacturer_id = man.virtuemart_manufacturer_id ';

        $query .= 'LEFT JOIN #__virtuemart_product_medias AS med_x ON med_x.virtuemart_product_id = prod.virtuemart_product_id ';

        $query .= 'LEFT JOIN #__virtuemart_medias AS med ON med_x.virtuemart_media_id = med.virtuemart_media_id ';


        $where = array(
            ' prod.published = 1 ',
            ' med.file_is_downloadable = 0 ',
            ' med.file_is_forSale = 0 '
        );

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'cat_x.virtuemart_category_id IN (' . implode(',', $categories) . ') ';
        }

        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'man.virtuemart_manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
        }

        switch ($this->data->get('virtuemartfeatured', 0)) {
            case 1:
                $where[] = ' prod.product_special = 1 ';
                break;
            case -1:
                $where[] = ' prod.product_special = 0 ';
                break;
        }

        switch ($this->data->get('virtuemartinstock', 0)) {
            case 1:
                $where[] = ' prod.product_in_stock > 0 ';
                break;
            case -1:
                $where[] = ' prod.product_in_stock = 0 ';
                break;
        }

        $query .= 'WHERE ' . implode(' AND ', $where) . ' GROUP BY prod.virtuemart_product_id ';

        $order = N2Parse::parse($this->data->get('virtuemartproductsorder', 'prod_ext.product_name|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'currencydisplay.php');
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' . DS . 'product.php');
        $currency = CurrencyDisplay::getInstance();

        $data = array();
        $root = JURI::root(false);
        for ($i = 0; $i < count($result); $i++) {
            $productModel = new VirtueMartModelProduct();
            $p            = $productModel->getProduct($result[$i]['id'], TRUE, TRUE, TRUE, 1, 0);


            $url = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $result[$i]['id'];
            if (!empty($catID) && $catID != 0) {
                $url .= '&virtuemart_category_id=' . $result[$i]['category_id'];
            }

            $r = array(
                'title'       => $result[$i]['name'],
                'url'         => $url,
                'description' => $result[$i]['description']
            );

            $r['image'] = NextendImageFallBack::fallback($root, array(
                $result[$i]['image'] == 'images/stories/virtuemart/product/cart_logo.jpg' ? '' : $result[$i]['image']
            ), array(
                $result[$i]['description'],
                $result[$i]['short_description']
            ));

            if (!empty($result[$i]['thumbnail'])) {
                $r['thumbnail'] = N2ImageHelper::dynamic($root . $result[$i]['thumbnail']);
            } else {
                $r['thumbnail'] = $r['image'];
            }

            $r += array(
                'price'                        => $currency->createPriceDiv('costPrice', '', $p->prices, true),
                'short_description'            => $result[$i]['short_description'],
                'category_name'                => $result[$i]['category_name'],
                'category_description'         => $result[$i]['category_description'],
                'category_url'                 => !empty($result[$i]['category_id']) ? 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $result[$i]['category_id'] : '',
                'manufacturer_name'            => $result[$i]['manufacturer_name'],
                'manufacturer_description'     => $result[$i]['manufacturer_description'],
                'manufacturer_email'           => $result[$i]['manufacturer_email'],
                'manufacturer_url'             => $result[$i]['manufacturer_url'],
                'base_price'                   => $currency->createPriceDiv('basePrice', '', $p->prices, true),
                'base_price_variant'           => $currency->createPriceDiv('basePriceVariant', '', $p->prices, true),
                'base_price_with_tax'          => $currency->createPriceDiv('basePriceWithTax', '', $p->prices, true),
                'discounted_price_without_tax' => $currency->createPriceDiv('discountedPriceWithoutTax', '', $p->prices, true),
                'price_before_tax'             => $currency->createPriceDiv('priceBeforeTax', '', $p->prices, true),
                'sales_price'                  => $currency->createPriceDiv('salesPrice', '', $p->prices, true),
                'tax_amount'                   => $currency->createPriceDiv('taxAmount', '', $p->prices, true),
                'sales_price_with_discount'    => $currency->createPriceDiv('salesPriceWithDiscount', '', $p->prices, true),
                'sales_price_temp'             => $currency->createPriceDiv('salesPriceTemp', '', $p->prices, true),
                'unit_price'                   => $currency->createPriceDiv('unitPrice', '', $p->prices, true),
                'price_without_tax'            => $currency->createPriceDiv('priceWithoutTax', '', $p->prices, true),
                'discount_amount'              => $currency->createPriceDiv('discountAmount', '', $p->prices, true),
                'sku'                          => $result[$i]['sku'],
                'id'                           => $result[$i]['id']
            );

            $data[] = $r;
        }
        return $data;
    }
}
