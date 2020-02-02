<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'config.php');
if(!class_exists("Action")) require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'startup.php');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'startup' . DIRECTORY_SEPARATOR . 'seo_url.php');

class N2DBConnectorJCart
{

    static function escape($var)
    {
        return JFactory::getDbo()->escape($var);
    }

    public function query($query)
    {
        $db               = new N2DBConnector('N2DBConnectorJCart');
        $dbResult         = $db->queryRow($query);
        $result           = new stdClass();
        $result->num_rows = count($dbResult);
        if (!empty($dbResult)) {
            $result->rows = $dbResult;
            foreach ($dbResult AS $key => $value) {
                $result->row[$key] = $value;
            }
        } else {
            $result->rows           = null;
            $result->row['keyword'] = null;
            $result->row['name']    = null;
        }
        return $result;
    }
}

class N2ControllerStartupSeoUrl extends ControllerStartupSeoUrl
{
    public $config;
    public $db;
    public $session;

    public function __construct()
    {
        $this->config = new Config();
        global $jCartSmartSliderLanguage;
        if (empty($jCartSmartSliderLanguage)) {
            $jCartSmartSliderLanguage = 0;
        }
        $this->config->set('config_language_id', intval($jCartSmartSliderLanguage));
        $model    = new N2DBConnectorJCart();
        $this->db = new StdClass();
        $this->db = $model;
        $this->session->data['language'] = '';
    }
}

class N2GeneratorJCartProducts extends N2GeneratorAbstract
{
    protected function _getData($count, $startIndex)
    {
        if (!defined('HTTP_SERVER')) {
            define('HTTP_SERVER', JURI::base());
        }

        $categories = array_map('intval', explode('||', $this->data->get('jcartsourcecategories', '0')));
        $where      = array('p.status = 1');

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'ptc.category_id IN (' . implode(',', $categories) . ') ';
        }

        switch ($this->data->get('jcartsourceinstock', 0)) {
            case 1:
                $where[] = ' p.quantity > 0 ';
                break;
            case -1:
                $where[] = ' p.quantity = 0 ';
                break;
        }

        $jNow = JFactory::getDate();
        $date = $jNow->format('Y-m-d');

        switch ($this->data->get('jcartsourceavailable', 0)) {
            case 1:
                $where[] = " p.date_available <= '" . $date . "'";
                break;
            case -1:
                $where[] = " p.date_available > '" . $date . "'";
                break;
        }

        $language = $this->data->get('jcartsourcelanguage', 0);
        if ($language != 0) {
            $where[] = ' pd.language_id  = ' . $language;
        }
        global $jCartSmartSliderLanguage;
        $jCartSmartSliderLanguage = $language;

        switch ($this->data->get('jcartsourceshipping', 0)) {
            case 1:
                $where[] = ' p.shipping = 1 ';
                break;
            case -1:
                $where[] = ' p.shipping = 0 ';
                break;
        }

        $minimum = $this->data->get('jcartsourceminimum', 0);
        if ($minimum != 0) {
            $where[] = ' p.minimum  >= ' . $minimum;
        }

        $query = "SELECT pd.name, pd.description, pd.tag, pd.meta_title, pd.meta_description, pd.meta_keyword,
                    p.image, p.price, p.weight, p.length, p.width, p.height, p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.location, p.points, p.quantity, p.product_id,
                    cd.name AS category_name, cd.description AS category_description, cd.category_id
                    FROM " . DB_PREFIX . "product_to_category AS ptc
                    LEFT JOIN " . DB_PREFIX . "product AS p ON ptc.product_id = p.product_id
                    LEFT JOIN " . DB_PREFIX . "product_description AS pd ON p.product_id = pd.product_id
                    LEFT JOIN " . DB_PREFIX . "category_description AS cd ON ptc.category_id = cd.category_id
                    WHERE " . implode(' AND ', $where) . ' GROUP BY ptc.product_id';

        $order = N2Parse::parse($this->data->get('jcartorder', 'p.date_added|*|desc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $model  = new N2Model('jcart_product');
        $result = $model->db->queryAll($query);

        $query    = "SELECT * FROM " . DB_PREFIX . "currency WHERE code = (SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' LIMIT 1) LIMIT 1";
        $currency = $model->db->queryRow($query);

        $data = array();
        $uri  = N2Uri::getBaseUri();
        $url  = new Url($uri . '/');
        $seo  = new N2ControllerStartupSeoUrl();

        $itemID = $this->data->get('jcartitemid', '');
        if (!empty($itemID)) {
            $itemID = '&Itemid=' . $itemID;
        } else {
            $itemID = '';
        }

        foreach ($result AS $res) {
            $r = array(
                'title' => $res['name'],
                'description' => $res['description']
            );

            $r['thumbnail'] = $r['image'] = NextendImageFallBack::fallback($uri . "/", array(
                !empty($res['image']) ? JCART_RELATIVE_URL . 'image/' . $res['image'] : ''
            ), array(
                str_replace('../components/com_jcart', 'components/com_jcart', $res['description'])
            ));

            $productUrl     = $this->changeAnd($url->link('product/product', '&product_id=' . $res['product_id'] . $itemID));
            $seoUrl         = $this->changeAnd($seo->rewrite($productUrl));
            $categoryUrl    = $this->changeAnd($url->link('product/category', '&path=' . $res['category_id'] . $itemID));
            $categorySeoUrl = $this->changeAnd($seo->rewrite($categoryUrl));

            $r += array(
                'url' => $productUrl,
                'seo_url' => $seoUrl,
                'category_url' => $categoryUrl,
                'seo_category_url' => $categorySeoUrl,
                'price' => $this->format($res['price'], $currency),
                'price_without_format' => $this->format($res['price'], $currency, false),
                'weight' => round($res['weight'], 2),
                'length' => round($res['length'], 2),
                'width' => round($res['width'], 2),
                'height' => round($res['height'], 2),
                'model' => $res['model'],
                'sku' => $res['sku'],
                'upc' => $res['upc'],
                'ean' => $res['ean'],
                'jan' => $res['jan'],
                'isbn' => $res['isbn'],
                'mpn' => $res['mpn'],
                'location' => $res['location'],
                'points' => $res['points'],
                'quantity' => $res['quantity'],
                'tag' => $res['tag'],
                'meta_title' => $res['meta_title'],
                'meta_description' => $res['meta_description'],
                'meta_keyword' => $res['meta_keyword'],
                'category_name' => $res['category_name'],
                'category_description' => $res['category_description'],
                'category_id' => $res['category_id'],
                'id' => $res['product_id']
            );
            $data[] = $r;
        }

        return $data;
    }


    private function format($number, $currency = '', $format = true)
    {
        $symbol_left   = $currency['symbol_left'];
        $symbol_right  = $currency['symbol_right'];
        $decimal_place = $currency['decimal_place'];
        $value         = $currency['value'];

        $amount = $value ? (float)$number * $value : (float)$number;

        $amount = round($amount, (int)$decimal_place);

        if (!$format) {
            return $amount;
        }

        $string = '';

        if ($symbol_left) {
            $string .= $symbol_left;
        }

        $string .= number_format($amount, (int)$decimal_place);

        if ($symbol_right) {
            $string .= $symbol_right;
        }

        return $string;
    }

    private function changeAnd($var)
    {
        return str_replace('&amp;', '&', $var);
    }

}
