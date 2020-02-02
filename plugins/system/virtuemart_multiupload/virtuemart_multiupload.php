<?php
/*------------------------------------------------------------------------
* Netbase Virtuemart Multiupload Plugin
* author : Netbase Team
* copyright Copyright (C) 2012 www.cms-extensions.net All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: www.cms-extensions.net
* Technical Support:  Forum - www.cms-extensions.net
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );

/**
 * Virtemart Multiupload system plugin
 */
class plgSystemVirtuemart_multiupload extends JPlugin {
	/**
	 *
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 */
	function plgSystemTest( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}

	function onAfterDispatch() {
        //only load in option=com_virtuemart&view=product&task=edit
        if (!$this->isPage('com_virtuemart', 'product' ,'edit')) return;

        $p_id =JRequest::getVar('virtuemart_product_id');
	    $document = &JFactory::getDocument();
        $document->addStyleSheet(JURI::root(true) . '/plugins/system/virtuemart_multiupload/assets/css/style.css');
        $document->addScript    (JURI::root(true) . '/plugins/system/virtuemart_multiupload/assets/js/nx.fileuploader.js');

        $base_path = JURI::root();
        $virtuemart_product_ids = implode(',', JRequest::getVar('virtuemart_product_id', array(), 'default', 'array')) ;
        $token = JSession::getFormToken();
        $js = "
			var iCount=0;
            function createUploader(){
            var uploader = new qq.FileUploader({

                element: document.getElementById('file-uploader'),
                listElement: document.getElementById('separate-list'),
                action:'".$base_path."plugins/system/virtuemart_multiupload/ajax/process.php',
                debug: false,
                params: {virtuemart_product_id:".$virtuemart_product_ids.",token:'".$token."'},
                onComplete:function(id, fileName,result){

                    // console.log(fileName)
                    // console.log(result)

					iCount = iCount+1;
					var li=jQuery('#separate-list').find('li');
					var iTotal=li.length;
						if(iCount==iTotal){
							window.location.reload();
						}
	                }
	            });
	        }
		";

            if( ($this->isPage('com_virtuemart', 'product' ,'edit')) && ($p_id > 0)) {
                $document->addScriptDeclaration($js);
            }
	}
	public function onAfterRender() {

		if (!$this->isPage('com_virtuemart', 'product' ,'edit')) return;

		//append javascript at the end of page
		$this->appendJS('nx.fileuploader.func.js', 'plugins/system/virtuemart_multiupload/assets/js/');
	}

	protected function isPage($option,$view,$task=null,$layout=null) {

		$input = JFactory::getApplication()->input;
		if($input->get('option', '', 'cmd') != $option) return false;
		if($input->get('view', '', 'cmd') != $view) return false;
		if($task)
			if($input->get('task', '', 'cmd') != $task) return false;
		if($layout)
			if($input->get('layout', '', 'cmd') != $layout) return false;

		return true;
	}

	// only call onAfterRender
	protected function appendJS($jsfile,$path) {
		$buffer = JResponse::getBody();
		$root	= JURI::root(true).'/';
		$js = '<script src="'.$root.$path.$jsfile.'" type="text/javascript"></script>';
		$buffer = str_ireplace('</body>', $js.'</body>', $buffer);
		JResponse::setBody($buffer);
	}
}
?>
