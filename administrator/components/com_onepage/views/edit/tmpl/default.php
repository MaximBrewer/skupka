<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

		$type = JRequest::getCmd('tr_type', 'site');
//		echo '<input type="hidden" name="tr_type" value="'.$type.'" id="tr_type" />'; 
	    $lang = JRequest::getVar('tr_tolang', ''); 
		$tr_from = JRequest::getVar('tr_fromlang', ''); 
		
		$ext = JRequest::getVar('tr_ext', ''); 
		$ext = str_replace('.ini', '', $ext); 
	    JHTMLOPC::script('translation_helper.js', 'administrator/components/com_onepage/assets/');
	    $document = JFactory::getDocument();
	    $javascript = ' var op_secureurl = "'.JURI::base().'index.php?option=com_onepage&view=edit&format=raw&tmpl=component&tlang='.$lang.'&tcomponent='.$ext.'&ttype='.$type.'&tr_from='.$tr_from.'"; op_css = false; ';
	    $document->addScriptDeclaration($javascript);

		$lango = JFactory::getLanguage(); 
		$lango = JFactory::getLanguage();
	    $extension = 'com_onepage';
		$tag = $lango->getTag();
		if (file_exists(JPATH_ADMINISTRATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.'.$extension.'.ini'))
		{
			$lang->load($extension, JPATH_ADMINISTRATOR, $tag);
		}
	
$style = '#toolbar-box {
	display: none;
	}';
$document->addStyleDeclaration( $style );
		
?>

<h1><?php echo JText::_('COM_ONEPAGE_WELCOME_TO_TRANSLATOR'); ?></h1>
<h2><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_H2'); ?></h2>
<p><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_HOWDOESITWORK'); ?></p> 
<p><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_HOWDOESITWORK_DESC'); ?></p>
<form action="index.php" method="post" onsubmit="javascript: return new function() { sb = document.getElementById('us21').disabled=true;  return true; }" >
  <div><p><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_HOWDOESITWORK_DESC2'); ?></p>
  </div>

	<?php $ni = 0; ?>
	<div class="adminlist" style="width: 100%;">
	 <?php 

	 if (!empty($this->vars[$type]))
	 if (empty($this->vars[$type][$lang]))
	 {
	 ?>
	 <p style="color: red;"><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_ERROR'); ?></p>
	 <?php
	 }
	 if (!empty($this->vars))
	 if (!empty($lang))
	 foreach($this->vars[$type][$lang] as $key3=>$val) {
	 
	  if (empty($val['var'])) continue; 
	  
	  //if (strpos($key3, '_defaulttrans')!==false) 
	  
	  {
	  ?>
	 <div class="row0" style="clear: both;">
	  <div class="key" style="clear: left; width: 300px; float: left; ">
	    <?php 
	     $key = $val['var'].'_translationid_'.$val['id'];
		 
		 $purekey = $val['var']; 
		 if (strpos($purekey, 'translationid'))
		  {

		  }
	     $localkey = $val['var']; 
	    // $enkey = substr($key, 0, strpos($key, '_translationid')); 
		// $enkey .= '_defaulttrans'; 
		 

	    if (!isset($this->vars[$type][$tr_from][$purekey])) 
	    { 
		 if (!empty($this->vars[$type][$lang][$purekey]['translation']))
		 $string = '"'.$this->vars[$type][$lang][$purekey]['translation'].'"'; 
		 else $string = JText::_('COM_ONEPAGE_MISSING_IN_ORIGINAL_FILE'); 
	    }
		else
	    $string = '"'.$this->vars[$type][$tr_from][$purekey]['translation'].'"'; 
	    
	    
	    //$string = str_replace('>', '&lg;', $string); 
	    //$string = str_replace('<', '&lg;', $string); 
	    echo $purekey.'<br />';
	    echo htmlentities($string, ENT_NOQUOTES, 'UTF-8'); 
	    
		?>
	   
	  </div>
	  <div style="float: left; clear: right;">
	   <?php 
	    $n = $type.'_'.'lang_'.$lang.'_'.$key;
	   ?>
	   <textarea style="float: left;" onblur="javascript: op_runSST(this, 'update'); " name="<?php echo $n; ?>" rows="3" cols="40"><?php
	    echo htmlentities($val['translation'], ENT_NOQUOTES, 'UTF-8'); 
	   ?></textarea>
	   <?php 
	   if (!isset($this->vars[$type][$tr_from][$purekey]))
	   {
	     echo JText::_('COM_ONEPAGE_THIS_IS_MISSING_IN_ORIGINAL').' ('.$tr_from.')!  <br />'; 
	   }
	   else
	   {
	   
	   if ($val['translation'] == $this->vars[$type][$tr_from][$purekey]['translation']) 
	   echo '<b style="color: red;">'.JText::_('COM_ONEPAGE_IDENTICAL').'</b>'; 
	   }
	   ?>
	   <div id="<?php echo 'hash'.md5($n.'_span'); ?>" style="float: left;">&nbsp;</div>
	   <?php 
	   echo '<br style="clear: both;"/><div style="width: 100%;">';
	   if (!empty($val['other']))
	   {
	   echo JText::_('COM_ONEPAGE_OTHER').'<br />';
	   
	   foreach ($val['other'] as $key2)
	   {
	     echo $this->vars[$type][$key3.'_translationid_'.$key2];
	   }
	   }
	   ?>
	   </div>
	  
	  </div>
	 </div>
	 <?php 
	  $ni ++; 
	  } // default trans
	 }
	
	  ?>
	</div>
	<?php 

	?>
   <input type="hidden" name="nickname" id="nickname" value="<?php 
   $user = JFactory::getUser(); 
   echo $user->username;
   ?>" />
   <?php 
   
   ?>
   <br style="clear: both;" />
   
   <input type="hidden" name="option" value="com_onepage" />                
   <input type="hidden" name="controller" value="edit" />               
   <input type="hidden" name="view" value="edit" />  
    
	 <?php
	 if (!empty($this->vars[$type][$lang])) 
	 {
	 ?>
   <div><?php echo JText::_('COM_ONEPAGE_TRANSLATOR_CLICK_HERE'); ?> </div>
   
   <div style="position: fixed; right:0; bottom:30px;">
   <div id="resp_msg" style="height: 200px; width: 200px; overflow-y:scroll; overflow-x: none;  background-color: yellow; opacity: 0.5;">&nbsp;<?php echo JText::_('COM_ONEPAGE_MESSAGE_WINDOW'); ?></div>
   <input type="button"  style=" height:40px; width: 200px; background-color: green; color: white; font-weight: bold;" onclick="javascript: return op_runSST(this, 'generate');" name="generate_file" id="hashgenerate" value="<?php echo str_replace('"', '\"', JText::_('COM_ONEPAGE_GENERATE_INI')); ?>" />
   </div>
   <input type="hidden" name="lang_code" value="<?php 
    // security
    $code = JRequest::getVar('tr_tolang'); 
	if (strlen($code)==5 || (strlen($code)==6)) 
	echo $code; 
	else 
	 {
	  $app	= JFactory::getApplication();
	  $app->redirect('index.php?option=com_onepage');
	 //echo 'improper input detected!';    
	 }
   ?>" />
   <input type="hidden" name="ttype" value="administrator" />
   <?php 
   //if ($type == 'site')
   {
   ?>
   <input type="hidden" name="task" value="display" />   
   
   <?php
   }
   }
  
   ?>
</form>


