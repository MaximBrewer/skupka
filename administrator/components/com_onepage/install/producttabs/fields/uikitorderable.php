<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
	  
jimport('joomla.form.formfield');
class JFormFieldUikitorderable extends JFormField
{
  protected $type = 'uikitorderable';
  function getInput()
	{
		//debug_zval_dump($this->form->data->get('module')); die(); 
		$values = (string)$this->element['values']; 
		$label = (string)$this->element['label']; 
		
		
		$values = explode(',', $values); 
		$v2 = array(); 
		foreach ($values as $k=>$v) {
		  $v2[$v] = $v; 
		}
		$values = $v2; 
		
		
		
		if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
		
		JHtml::script('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/js/uikit.min.js'); 	
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/uikit.min.css'); 
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/components/sortable.min.css'); 
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/components/sortable.gradient.min.css'); 
		JHtml::script('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/js/components/sortable.js'); 	
		
		
		$root = Juri::root(); 
		if (substr($root, -1) !== '/') $root .= '/'; 
		$sor = $root.'plugins/system/producttabs/fields/uikitorderable.js'; 
		JHtml::script($sor); 
		$sor = $root.'plugins/system/producttabs/fields/uikitorderable.css'; 
		JHtml::stylesheet($sor); 
		
		//if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'themes'.DS.'extra'.DS.'uikit')) 
		{
		  if ( version_compare( JVERSION, '3.0', '>' ) == 1) {    
		   JHtml::_('jquery.framework');
		  }
		  jimport('joomla.filesystem.file');
		  $nm = 'id_'.JFile::makeSafe($this->name); 
		  
		  $root = Juri::root(); 
		  $html = '<div class="clearfix clearboth" >'; 
         $html .= '<div class="uk-container uk-container-center" >';
		 $html .= '<input type="hidden" value="'.$this->value.'" name="'.$this->name.'" id="'.$nm.'" />'; 
		 
		  $ar = $this->getArray($this->value);
		  
		  $html .= '<div class="uk-sortable " data-uk-sortable="data-uk-sortable" data-group="'.$nm.'">'; 
		  //$html .= '<li data-id="1"><div class="uk-sortable-handle"></div>...</li>'; 
		  foreach ($ar as $k=>$v) {
		  $html .= '
		  <div class="sortrow " data-val-id="'.$nm.'" data-val="'.$v.'" data-id="'.$k.'" data-group="'.$nm.'">
		  
		     <div class="uk-panel uk-panel-box">
		     <i class="uk-sortable-handle uk-icon uk-icon-bars uk-margin-small-right"></i>
			 <strong>'.JText::_($v).'</strong>
			 <div class="k-badge uk-badge-notification uk-panel-badge">
			  <i class="uk-icon uk-icon-remove uk-margin-small-left uk-cursor-pointer" data-group="'.$nm.'" data-val="'.$v.'" data-id="'.$k.'"></i>
			 </div>
			 </div></div>'; 
			 unset($values[$v]); 
		  }
		  
		  $html .= '<h2 class="unused_separator sortrow" data-group="'.$nm.'">'.JText::_('PLG_SYSTEM_PRODUCTTABS_UNUSED').'</h2>'; 
		  $html .= '<div class="unused_values" data-group="'.$nm.'">'; 
		  $html .= '</div>'; 
		  if (!empty($values)) 
			  foreach ($values as $k=>$v) {
			      $html .= '
		  <div class="sortrow " data-val-id="'.$nm.'" data-val="'.$v.'" data-id="'.$k.'" data-group="'.$nm.'">
		  
		     <div class="uk-panel uk-panel-box">
		     <i class="uk-sortable-handle uk-icon uk-icon-bars uk-margin-small-right"></i>
			 <strong>'.JText::_($v).'</strong>
			 <div class="k-badge uk-badge-notification uk-panel-badge">
			  <i class="uk-icon uk-icon-remove uk-margin-small-left uk-cursor-pointer" data-group="'.$nm.'" data-val="'.$v.'" data-id="'.$k.'"></i>
			 </div>
			 </div></div>'; 
			  }
			  
		  
		  
	      $html .= '</div>'; 
		  //$html .= '<button type="button" class="uk-button uk-button-success">Save</button>'; 
		  $html .= '</div></div>';
		  return $html; 
		  
		  
		  
		}
	
	}
	private function getArray($value) {
	   $a = explode(',', $value); 
	   foreach ($a as $k=>$v) {
	     $a[$k] = trim($v); 
	   }
	   return $a; 
	}
	
	
}

