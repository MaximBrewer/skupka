<?php
/**
 *
 * Customfilters products view
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 */

// No direct access
defined('_JEXEC') or die();

require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'cfview.php';

class CustomfiltersViewProducts extends cfView
{
    /**
     *
     * @var string
     */
    public $vm_version;

    /**
     *
     * @var int
     */
    public $show_prices;

    /**
     * Display function of the view
     *
     * @see cfView::display()
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $this->show_prices = (int)VmConfig::get('show_prices', 1);
        $this->addHelperPath(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers');
        $this->load();
        $this->vm_version = VmConfig::getInstalledVersion();
        $this->showcategory = VmConfig::get('showCategory', 1);
        $this->showproducts = true;
        $this->showsearch = false;

        // get menu parameters
        $this->menuParams = cftools::getMenuparams();
        $vendorId = 1;
        $jinput = $app->input;
        $this->fallback = false;

        $categories = $jinput->get('virtuemart_category_id', array(), 'array');

        /* If there is only one category selected and is not zero, display children categories */
        if (count($categories) == 1 && isset($categories[0]) && $categories[0] > 0) {
            $this->categoryId = (int)$categories[0];
            if($this->showcategory) {
                $category_haschildren = true;
            }
        } else {
            $this->categoryId = 0;
            $category_haschildren = false;
        }

        $categoryModel = VmModel::getModel('category');
        $category = $categoryModel->getCategory($this->categoryId);
        $catImgAmount = VmConfig::get('catimg_browse', 1) ? VmConfig::get('catimg_browse', 1) : 1;
        $categoryModel->addImages($category, $catImgAmount);
        $category->haschildren = $category_haschildren;

        if ($category_haschildren) {
            $category->children = $categoryModel->getChildCategoryList($vendorId, $this->categoryId, $categoryModel->getDefaultOrdering(), $categoryModel->_selectedOrderingDir);
            $categoryModel->addImages($category->children, $catImgAmount);
        }

        // triggers a content plugn for that category
        if (VmConfig::get('enable_content_plugin', 0)) {
            if (method_exists('shopFunctionsF', 'triggerContentPlugin')) {
                shopFunctionsF::triggerContentPlugin($category, 'category', 'category_description');
            }
        }

        $this->category = $category;
        $this->setVariablesFromParams();

        // load basic libraries before any other script
        $template = VmConfig::get('vmtemplate', 'default');
        if (is_dir(JPATH_THEMES . DIRECTORY_SEPARATOR . $template)) {
            $mainframe = JFactory::getApplication();
            $mainframe->set('setTemplate', $template);
        }
        $this->_prepareDocument();

        /*
         * show base price variables
         */
        $user = JFactory::getUser();
        $this->showBasePrice = ($user->authorise('core.admin', 'com_virtuemart') or $user->authorise('core.manage', 'com_virtuemart'));

        /*
         * get the products from the cf model
         */
        $productModel = VmModel::getModel('product');

        // rating
        $ratingModel = VmModel::getModel('ratings');
        $this->showRating = $ratingModel->showRating();
        $productModel->withRating = $this->showRating;

        $ids = $this->get('ProductListing');
        $this->products = $productModel->getProducts($ids);

        $productModel->addImages($this->products);
        $model = $this->getModel();

        if ($this->products) {            
            shopFunctionsF::sortLoadProductCustomsStockInd($this->products, $productModel);
        }

        $productsLayout = VmConfig::get('productsublayout', 'products');
        if (empty($productsLayout)) {
            $productsLayout = 'products';
        }
        $this->productsLayout = $productsLayout;
        // currency
        $currency = CurrencyDisplay::getInstance();
        $this->currency = $currency;

        /*
         * vm 3.0.18 and later saves the products in an assoc. array using as a key the product type
         * @todo Check that in later versions
         */
        $this->fallback = false;
        if (version_compare($this->vm_version, '4.0') > 0) {
            $products = $this->products;
            $this->products = [];
            $this->fallback = true;
            $this->products['0'] = $products;
        }
        // lower to 4.0
        else {
            $this->fallback = true;
            vmdebug('Fallback active');
        }

        $this->search = false;
        $this->searchcustom = '';
        $this->searchCustomValues = '';
        $this->add_product_link= '';

        // my model's pagination
        $this->vmPagination = $model->getPagination(true);
        $this->perRow = $this->menuParams->get('prod_per_row', 3);
        $this->orderByList = $this->get('OrderByList');

        parent::display($tpl);
        if (empty($this->products)) {
            echo '<span class="cf_results-msg">' . JText::_('COM_CUSTOMFILTERS_NO_PRODUCTS') . '</span>';
        }
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $document = JFactory::getDocument();
        $app = JFactory::getApplication();
        $joomla_conf = JFactory::getConfig();
        $this->setCanonical();

        /*
         * Add meta data
         */
        if ($this->categoryId > 0) {
            if (! empty($this->category->metadesc)) {
                $document->setDescription($this->category->metadesc);
            }
            if (! empty($this->category->metakey)) {
                $document->setMetaData('keywords', $this->category->metakey);
            }
            if (! empty($this->category->metarobot)) {
                $document->setMetaData('robots', $this->category->metarobot);
            }
            if ($joomla_conf->get('MetaAuthor') == true && ! empty($this->category->metaauthor)) {
                $document->setMetaData('author', $this->category->metaauthor);
            }
        }

        /*
         * Load scripts and styles
         */
        cftools::loadScriptsNstyles();

        // layout
        $this->_setPath('template', (JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'category' . DIRECTORY_SEPARATOR . 'tmpl'));
        $layout = $this->menuParams->get('cfresults_layout');
        $this->setLayout($layout);

        // load the virtuemart language files
        if (method_exists('VmConfig', 'loadJLang')) {
            VmConfig::loadJLang('com_virtuemart', true);
        }
        else {
            $language = JFactory::getLanguage();
            $language->load('com_virtuemart');
        }
    }

    /**
     *
     * Add canonical urls to the head of the pages
     * If there is another canonical replaces it with a new one
     *
     * @since 2.2.0
     */
    public function setCanonical()
    {
        $document = JFactory::getDocument();
        $inputs = CfInput::getInputs();

        if (count($inputs) == 1) {
            if (isset($inputs['virtuemart_category_id'])) {
                $currentlink = '&virtuemart_category_id=' . (int) reset($inputs['virtuemart_category_id']);
            } else
                if (! empty($inputs['virtuemart_manufacturer_id'])) {
                    $currentlink = '&virtuemart_manufacturer_id=' . (int) reset($inputs['virtuemart_manufacturer_id']);
                }
        }

        if (! empty($currentlink)) {
            $canonical_url = JRoute::_('index.php?option=com_virtuemart&view=category' . $currentlink);

            $links = $document->_links;
            foreach ($links as $key => $link) {
                if (is_array($link)) {
                    if (array_key_exists('relation', $link) && ! empty($link['relation']) && $link['relation'] == 'canonical') {
                        // found it - delete the old
                        unset($document->_links[$key]);
                    }
                }
            }
            // add a new one
            $document->_links[$canonical_url] = array(
                'relType' => 'rel',
                'relation' => 'canonical',
                'attribs' => ''
            );
        }
    }

    /**
     * Load external files if they miss
     *
     * @return CustomfiltersViewProducts
     */
    public function load()
    {
        if ($this->show_prices == 1) {
            if (! class_exists('calculationHelper')) {
                require (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'calculationh.php');
            }
        }

        if (! class_exists('CurrencyDisplay')) {
            require (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
        }

        if (! class_exists('shopFunctionsF')) {
            require (JPATH_VM_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctionsf.php');
        }

        if (! class_exists('VirtueMartModelCategory')) {
            require (JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'category.php');
        }

        if (!class_exists('VmImage') && file_exists(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR. 'image.php')) {
            require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR. 'image.php');
        }

        return $this;
    }

    /**
     * Set variables from the config params
     *
     * @return CustomfiltersViewProducts
     */
    protected function setVariablesFromParams()
    {
        $params = [
            'itemid' => '',
            'categorylayout' => VmConfig::get('categorylayout', 0),
            'show_store_desc' => VmConfig::get('show_store_desc', 1),
        	'show_pcustoms' => VmConfig::get('show_pcustoms', 1),
            'showcategory_desc' => VmConfig::get('showcategory_desc', 1),
            'showcategory' => VmConfig::get('showcategory', 1),
            'categories_per_row' => VmConfig::get('categories_per_row', 3),
            'showproducts' => true,
            'showsearch' => false,
            'keyword' => false,
            'productsublayout' => VmConfig::get('productsublayout', 0),
            'products_per_row' => $this->menuParams->get('prod_per_row', 3),
            'featured' => VmConfig::get('featured', 0),
            'featured_rows' => VmConfig::get('featured_rows', 1),
            'discontinued' => VmConfig::get('discontinued', 0),
            'discontinued_rows' => VmConfig::get('discontinued_rows', 1),
            'latest' => VmConfig::get('latest', 0),
            'latest_rows' => VmConfig::get('latest_rows', 1),
            'topten' => VmConfig::get('topten', 0),
            'topten_rows' => VmConfig::get('topten_rows', 1),
            'recent' => VmConfig::get('recent', 0),
            'recent_rows' => VmConfig::get('recent_rows', 1)
        ];

        foreach ($params as $param => $value) {
            //these params cannot change
            if($param == 'showproducts' || $param == 'showsearch' || $param == 'keyword') {
                $this->$param = $value;
                continue;
            }

            if (empty($this->categoryId) || empty($this->category->$param)) {
                $this->$param = $this->menuParams->get($param, $value);
            } else
                if (isset($this->category->$param)) {
                    $this->$param = $this->category->$param;
                }
        }
        return $this;
    }
}
