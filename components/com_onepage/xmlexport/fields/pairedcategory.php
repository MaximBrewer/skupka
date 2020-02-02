<?php
defined('_JEXEC') or die();

/**
 * @version		
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 
jimport('joomla.form.formfield');

class JFormFieldPairedcategory extends JFormField {

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    var $_name = 'paired_category';

    function getInput() {
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		$x = get_defined_vars(); 
		$entity = $this->group; 
		$data = $this->value; 
		if (is_object($data)) {
		 $current_category = $data->current_category; 
		 $paired_category = $data->paired_category; 
		}
		else {
			return ''; 
		
		}
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'pairing.php'); 
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	static $done; 
	 if (empty($done)) {
	 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 		
	
	$css = ' fieldset.adminform { overflow: visible !important; } '; 
	
	
	

	 $doc = JFactory::getDocument(); 
	 $class = get_class($doc); 
	 $class = strtoupper($class); 
	 // never run in an ajax context !
	 $arr = array('JDOCUMENTHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT'); 
	 if (in_array($class, $arr)) 
	 {
		
	
	$doc->addStyleDeclaration($css); 
	JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
	
	
	}
	else
	{

		
	}
	}
	$done = true; 
	
	
	 $pairing = new JModelPairing(); 
	 //function getData($entity='', $type='', $category_id=0, $asset='')
	 $this->data = $pairing->getData($entity, 'xmlexport', 'virtuemart_category_id'); 
	
		

ob_start(); 
?><select class="vm-chzn-select" name="<?php echo $this->name; ?>" onchange="updateCat(this)" ><?php

foreach ($this->data as $id=>$txt)
 {
   //$extoptions .= '<option value="'.$id.'">'.$txt.'</option>'; 
   //renderOption($entity, $vmCat, $refCat, $txt)
   echo $pairing->renderOption($entity, $current_category, $id, $txt); 
 }
 ?></select><div id="cat_id_<?php echo $current_category; ?>_<?php echo $entity; ?>">&nbsp;</div>
<?php
$html = ob_get_clean(); 
	
return $html; 
    }

} 