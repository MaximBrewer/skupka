<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
// The class name must always be the same as the filename (in camel case)
class JFormFieldMailchimp extends JFormField {
 
        //The field class must know its own type through the variable $type.
        protected $type = 'mailchimp';
		public $value; 
		public function getTitle()
		{
			
			$this->value = JText::_('PLG_SYSTEM_VM_MAILCHIMP_NOTSUBSCRIBED'); 
			if (class_exists('plgSystemVm_mailchimp'))
			{
				if (!empty(plgSystemVm_mailchimp::$mailchimp_wasregistered))
				{
					$this->value = JText::_('PLG_SYSTEM_VM_MAILCHIMP_SUBSCRIBED'); 
				}
			}
			// PROFILE_VALUE_NOT_FOUND
			//$x = debug_backtrace(); foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />"; die(); 
			return JText::_('PLG_SYSTEM_VM_MAILCHIMP_SLIDER_LABEL'); 
		}
		
		
		
		
         public function getLabel() {
                // code that returns HTML that will be shown as the label
				
				if (class_exists('plgSystemVm_mailchimp'))
				{
				   if (!empty(plgSystemVm_mailchimp::$mailchimp_title)) 
				   return '<label for="mailchimp_register">'.plgSystemVm_mailchimp::$mailchimp_title.'</label>'; 
				}
			
				return 'Subscribe to newsletter'; 
				
        }
 
        public function getInput() {
			$checked = $mailchimp_wasregistered = ''; 
			if (class_exists('plgSystemVm_mailchimp'))
				{
				   if (!empty(plgSystemVm_mailchimp::$mailchimp_checked)) 
				   $checked =  plgSystemVm_mailchimp::$mailchimp_checked; 
			      
				   if (!empty(plgSystemVm_mailchimp::$mailchimp_wasregistered)) 
					   $mailchimp_wasregistered = plgSystemVm_mailchimp::$mailchimp_wasregistered; 
			   
				}
			
                // code that returns HTML that will be shown as the form field
				return $mailchimp_wasregistered.'<input type="checkbox" id="mailchimp_register" name="mailchimp_register" value="1" '.$checked.' />'; 
        }
}