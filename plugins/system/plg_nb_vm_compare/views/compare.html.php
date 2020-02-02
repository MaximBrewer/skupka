<?php
/*------------------------------------
* System Compare Products for Virtuemart
* Author    CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
* Version 1.0.0
-----------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.html.parameter' );
jimport('joomla.application.component.view');
defined ('DS') or define ('DS', DIRECTORY_SEPARATOR);
if(!class_exists('VmView')) 
	require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmview.php');
 
class VirtueMartViewProductdetails extends VmView {
    protected $limit; 
    protected $success_compare;
    protected $max;
    protected $spacer;
    protected $img_class;
    public function display($tpl = null) {
        $app  	= JFactory::getApplication();
    	$doc  	= JFactory::getDocument();
    	$user	= JFactory::getUser();
        $dev	= true;
    	$virtuemart_product_id =  JRequest::getInt('virtuemart_product_id');
        $product_model = VmModel::getModel('product');
        $plugin = JPluginHelper::getPlugin('system', 'plg_nb_vm_compare');
        $pluginParams = new JRegistry($plugin->params);
        $this->max = $pluginParams->get('max_item');
        $this->spacer = $pluginParams->get('element_block');
        $this->img_class = $pluginParams->get('img_class');
	    ob_start();
        if(empty($_SESSION['compare'])){
            $_SESSION["compare"][$virtuemart_product_id] = $virtuemart_product_id;
            $this->success_compare = 1; 
        }else{
            if(count($_SESSION['compare']) == $this->max) $this->limit = 1;
            if(!in_array($virtuemart_product_id,$_SESSION['compare']) && !$this->limit){
                $_SESSION["compare"][$virtuemart_product_id] = $virtuemart_product_id;
                $this->success_compare = 1; 
            }else{
                $this->success_compare = 0; 
            }
            $product = $product_model->getProduct($virtuemart_product_id,TRUE,TRUE,TRUE,1);
        }
        $path = __DIR__.'/tmpl/';
        $this->addTemplatePath($path);
        parent::display($tpl);
        $output = ob_get_clean();
        echo $output;
        die;
    }
}

?>
