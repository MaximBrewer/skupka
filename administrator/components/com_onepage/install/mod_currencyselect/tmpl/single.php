<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication(); 
$t = $app->getTemplate(); 

$root = Juri::root(); 
if (substr($root, -1)!=='/') $root .= '/'; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'mod_currencyselect'.DIRECTORY_SEPARATOR.'mod_currencyselect.css'))
{
	
	JHtml::stylesheet($root.'templates/'.$t.'/html/mod_currencyselect/mod_currencyselect.css'); 
}
else
{
	JHtml::stylesheet($root.'modules/mod_currencyselect/tmpl/mod_currencyselect.css'); 
}



$app = JFactory::getApplication(); 
$t = $app->getTemplate(); 
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'mod_currencyselect'.DIRECTORY_SEPARATOR.'mod_currencyselect.js'))
{
	
	JHtml::script($root.'templates/'.$t.'/html/mod_currencyselect/mod_currencyselect.js'); 
}
else
{
	JHtml::script($root.'modules/mod_currencyselect/tmpl/mod_currencyselect.js'); 
	
}


?><form action="<?php echo vmURI::getCleanUrl() ?>" method="post" name="currency_form" >
 <div class="currency-label"><?php echo JText::_('MOD_CURRENCYSELECT_SELECTCURRENCY'); ?></div>
 <div class="cur_wrap">
  
   <?php 
   $i = 0; 
   $c = count($currencies); 
   foreach ($currencies as $k=>$v)
   {
	   
	   $i++; 
	   ?>
	   <div class="cur_wrapper cur_<?php echo strtolower($v->currency_code_3); ?> <?php if ($i === $c) echo ' cur_last '; else echo ' border_right '; ?>  cur_n_<?php echo $i; ?> <?php if ($i === 1) echo ' cur_first '; ?> <?php if ($virtuemart_currency_id == $v->virtuemart_currency_id) echo ' currency_selected '; ?> ">
	   <span class="cur_box curbox_<?php echo strtolower($v->currency_code_3); ?>">
	     <button class="cur_submit curbutton_<?php echo strtolower($v->currency_code_3); ?>" onclick="return changeCurrency(this);" rel="<?php echo $v->virtuemart_currency_id; ?>"><?php echo $v->currency_symbol; ?></button>
	   

	   </span>
	      
	   
	   </div>
	   <?php
	   
   }
   ?>
   <input type="hidden" name="virtuemart_currency_id" id="cur_virtuemart_currency_id" value="<?php echo $virtuemart_currency_id; ?>" />
   
  </div>
</form>
