<?php
/**
 * @copyright    Copyright (c) 2018 UploadProductFile image. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * vmcustom - UploadProductFile Plugin
 *
 * @package        Joomla.Plugin
 * @subpakage    UploadProductFile image.UploadProductFile
 */
class plgvmcustomUploadProductFile extends vmCustomPlugin
{

    /**
     * Constructor.
     *
     * @param    $subject
     * @param    array $config
     */
    function __construct(&$subject, $config = array())
    {
        // call parent constructor
        parent::__construct($subject, $config);

        $this->_tablename = '#__virtuemart_product_custom_plg_uploadphoto';
    }

    /**
     * Displays the custom field in the product view of the backend
     *
     * @param    object $field - The custom field
     * @param    int $product_id
     * @param    int $row - The a/a of that field within the product
     * @param    string $retValue - The html that regards the custom fields of that product
     * @since    1.0
     */
    function plgVmOnProductEdit($field, $product_id, &$row, &$retValue)
    {

        if ($field->custom_element != $this->_name) return '';
        $html = '
            <script>
            var page_id=' . $product_id . ';
            </script>

            <link href=/plugins/vmcustom/uploadproductfile/app/css/chunk-vendors.css rel=stylesheet>
            <link href=/plugins/vmcustom/uploadproductfile/app/css/app.css rel=stylesheet>

           <noscript>
              <strong>
                We\'re sorry but app doesn\'t work properly without JavaScript enabled. Please enable it tocontinue.
               </strong>
            </noscript>

             <div id=app></div>
			<script src=/plugins/vmcustom/uploadproductfile/app/js/chunk-vendors.js></script>
             <script src=/plugins/vmcustom/uploadproductfile/app/js/app.js></script>
			';
        $retValue .= $html;
        $row++;
        return true;
    }

    function plgVmOnDisplayProductFEVM3(&$product, &$group)
    {

        if ($group->custom_element != $this->_name) return '';

        $this_tablename = '#__virtuemart_product_custom_plg_uploadphoto';
        $html = "";


        $page_id = $product->virtuemart_product_id;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query = $db->getQuery(true)
            ->select('uploadphoto')
            ->from($db->quoteName($this_tablename))
            ->where($db->quoteName('virtuemart_product_id') . ' = ' . $page_id);
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            $plugin = JPluginHelper::getPlugin('vmcustom', 'uploadproductfile');
            if ($plugin) {
                $pluginParams = new JRegistry($plugin->params);
                $plugin_folders = $pluginParams->get('plugin_folders');
            } else {
                $plugin_folders = "phototexture";
            }
            $url = str_replace(JURI::root(true), "", JURI::root());
            $img_url = $url . 'images/' . $plugin_folders . '/';

            $items = json_decode($result);

            if ($items) {
                ob_start();
                ?>

                <div class="uploadproductfile">
                    <div class="uploadproductfile_wrap">
                        <?php
                        foreach ($items as $item) {
                            ?>
                            <div class="uploadproductfile_wrap_item">
                                <div class="uploadproductfile_wrap_item_title">
                                    <?php echo $item->title ?>
                                </div>
                                <?php
                                $items_imgs = $item->items;
                                if ($items_imgs) {
                                    ?>
                                    <div class="uploadproductfile_imgs">
                                        <?php
                                        foreach ($items_imgs as $key=>$items_img) {
                                            ?>
                                            <div class="uploadproductfile_imgs_item">

                                                <div class="uploadproductfile_imgs_item_wrap" data-parenttitle="<?php echo $item->title; ?>" data-title="<?php echo $items_img->title; ?>">
                                                    <div class="uploadproductfile_imgs_item_image">
                                                        <a href="<?php echo $img_url . $items_img->photo; ?>" title="<?php echo $item->title.': '.$items_img->title; ?>" class="upload_image-link">
                                                            <img src="<?php echo $img_url . $items_img->photo; ?>"
                                                                 alt=" <?php echo $items_img->title; ?>">
                                                        </a>
                                                    </div>
                                                    <div class="uploadproductfile_imgs_item_title">
                                                        <div class="select_upload" >
                                                            <?php echo $items_img->title; ?>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    // для корзины
                    $params=$group;
                    $name = 'customProductData['.$product->virtuemart_product_id.']['.$params->virtuemart_custom_id.']['.$params->virtuemart_customfield_id .'][upload]';

                    ?>
                    <input
                            value=""
                            type="hidden"
                            class="uploadproductfile_hidden"
                            size="<?php echo $params->custom_size ?>"
                            name="<?php echo $name?>" />
                </div>
                <script>
                    if(typeof uploadproductfileGlobal=="undefined"){
                        uploadproductfileGlobal=true;
                        if(jQuery().magnificPopup) {

                        }else{
                            var url='/plugins/vmcustom/uploadproductfile/libs/Magnific/jquery.magnific-popup.min.js';
                            document.write('<link rel="stylesheet" type="text/css" href="/plugins/vmcustom/uploadproductfile/libs/Magnific/magnific-popup.css" />');
                            document.write('<script src="' + url + '"><\/script>');
                            jQuery(document).ready(function($) {
                                $('.upload_image-link').magnificPopup({
                                    type: 'image'
                                });
                            });
                        }
                    }

                </script>
                <?php
                $html = ob_get_clean();
                $document=JFactory::getDocument();
                $document->addScript( JURI::root(true).'/plugins/vmcustom/uploadproductfile/js/uploadpoduct.js');
                $document->addStyleSheet( JURI::root(true).'/plugins/vmcustom/uploadproductfile/css/vmcustom.css');
            }
        }
        $group->display .= $html;
        return true;
    }

    /**
     * Trigger for VM3
     * @author Max Milbers
     * @param $product
     * @param $productCustom
     * @param $html
     * @return bool|string
     */
    function plgVmOnViewCartVM3(&$product, &$productCustom, &$html) {

        if (empty($productCustom->custom_element) or $productCustom->custom_element != $this->_name) return false;

        if(empty($product->customProductData[$productCustom->virtuemart_custom_id][$productCustom->virtuemart_customfield_id])) return false;
        foreach( $product->customProductData[$productCustom->virtuemart_custom_id] as $k =>$item ) {
            if($productCustom->virtuemart_customfield_id == $k) {
                if(isset($item['upload'])){
                    $html .='<span>'.vmText::_($productCustom->custom_title).' '.$item['upload'].'</span>';
                }
            }
        }
        return true;
    }

    function plgVmOnViewCartModuleVM3( &$product, &$productCustom, &$html) {
        return $this->plgVmOnViewCartVM3($product,$productCustom,$html);
    }

    function plgVmDisplayInOrderBEVM3( &$product, &$productCustom, &$html) {
        $this->plgVmOnViewCartVM3($product,$productCustom,$html);
    }

    function plgVmDisplayInOrderFEVM3( &$product, &$productCustom, &$html) {
        $this->plgVmOnViewCartVM3($product,$productCustom,$html);
    }


    /**
     * trigered when a product is cloned
     * This function is inconsistent as it tries to guess the next product_id and virtuemart_customfield_id
     * In case of multi-user environment will may fail due to concurrent insertions of products and custom fields
     *
     * @param object $product
     * @since	1.4.0
     */
    function plgVmCloneProduct($product){
        /*
         * In VM2 the virtuemart_product_id regards the initial product
         * In VM3 it regards the clone
         */
        if(version_compare(VM_VERSION, '2.9','lt')){
            if(!empty($product->virtuemart_product_id))$product_id=$product->virtuemart_product_id;
        }else $product_id=$product->originId;

        //get the next product id
        if(version_compare(VM_VERSION, '2.9','lt')){
            $query="SHOW TABLE STATUS LIKE '".$db->getPrefix()."virtuemart_products'";
            $db->setQuery($query);
            if(!$db->query())return;
            else {
                $tableStatus=$db->loadObject();
                $last_virtuemart_product_id=$tableStatus->Auto_increment;
                if(empty($last_virtuemart_product_id))return;
            }
            $new_product_id_entry=(int)$last_virtuemart_product_id;
        }
        //VM3 supplies as the cloned product id
        else $new_product_id_entry=$product->virtuemart_product_id;


        // echo "<pre>";
        // var_dump($product_id);
        // var_dump($new_product_id_entry);
        // echo "</pre>";


        $this_tablename = '#__virtuemart_product_custom_plg_uploadphoto';
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query = $db->getQuery(true)
            ->select('uploadphoto')
            ->from($db->quoteName($this_tablename))
            ->where($db->quoteName('virtuemart_product_id') . ' = ' .$product_id);
        $db->setQuery($query);
        $result =$db->loadResult();


        if($result){
            // вставляем
            $data = new stdClass();
            $data->virtuemart_product_id =$new_product_id_entry;
            $data->uploadphoto=$result;
            $result = $db->insertObject($this_tablename, $data);
        }

    }

}