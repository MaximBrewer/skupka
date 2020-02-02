<?php
/*--------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012-2017 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      https://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 3 $
$LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
$Id: vpdownloadkey.php 3 2017-09-20 14:30:08Z Abhshek Das $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;

defined('PHP_EOL') or define('PHP_EOL', "\n");
JLoader::register('VPDownloadKeyHelper', dirname(__FILE__) . '/vpdownloadkey/helper.php');

class JFormFieldVPDownloadKey extends JFormField
{
	protected $type = 'VPDownloadKey';
	
	protected function getInput()
	{
		$doc        = JFactory::getDocument();
		$app        = JFactory::getApplication();
		$today      = JFactory::getDate();
		$rel_path   = str_replace(JPATH_ROOT, '', dirname(__FILE__));
		$root       = JUri::root(true) . '/';
		$base_url   = $root . ltrim(str_replace('\\', '/', $rel_path), '/');
		$assets_url = $base_url . '/vpdownloadkey/assets';
		$jquery_url = isset($this->element['jqueryurl']) ? $this->element['jqueryurl'] : null;
		$html       = array();

		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			JHtml::_('jquery.framework');
		}
		elseif($jquery_url)
		{
			$doc->addScript($root . $jquery_url . '/jquery.min.js');
			$doc->addScript($root . $jquery_url . '/jquery-noconflict.js');
			$doc->addScript($root . $jquery_url . '/jquery-migrate.min.js');
		}
		
		// Load CSS
		$doc->addStyleSheet($assets_url . '/css/style.css?ver=5.2');
		
		// Load JS
		$doc->addScript($assets_url . '/js/script.min.js?ver=5.2');
		
		// Check for available adapters
		$adapter      = VPDownloadKeyHelper::getAdapter();
		$enabled      = $this->isEnabled();
		$data         = VPDownloadKeyHelper::decodeData($this->value);
		$this->value  = empty($data['dlk']) ? '' : $this->value;
		$last_checked = $data['last_checked'] ? JFactory::getDate($data['last_checked']) : $today;
		$required     = isset($this->element['required']) && $this->element['required'] == 'true' ? ' required="required"' : '';
		$required     = !$adapter ? '' : $required;
		$btn_class    = !empty($data['dlk']) ? ' vpdk-show-edit' : '';
		$clear_class  = !empty($data['dlk']) ? '' : ' vpdk-hide';
		$interval     = date_diff($last_checked, $today);
		$interval     = (int) $interval->format('%R%a'); // Interval in days
		$revalidate   = ($last_checked != $today) && ($interval > 7) ? ' data-vpdkvalidate="auto"' : '';
		$access_class = !empty($data['dlk']) && !$data['access'] ? '' : ' vpdk-hide';
		$valid_class  = !empty($data['dlk']) && $data['access'] ? '' : ' vpdk-hide';
		$def_class    = empty($data['dlk']) ? '' : ' vpdk-hide';
		$data_access  = !$data['access'] ? ' data-vpdkaccess="0"' : ' data-vpdkaccess="1"';
		$extension_id = JFactory::getApplication()->input->getInt('extension_id', 0);
		
		$html[] = '<input type="text" id="' . $this->id . '" value="'
		          . htmlspecialchars($data['dlk'], ENT_COMPAT, 'UTF-8') . '" data-vpdk="validate"' . $required . $data_access 
		          . ' autocomplete="off" spellcheck="false" readonly="readonly" />';
		$html[] = '<input type="hidden" id="' . $this->id . '-hidden" name="' . $this->name . '" value="' . $this->value . '" />';

		if(!$enabled)
		{
			if(!empty($required))
			{
				$html[] = '<button type="button" id="' . $this->id . '-enable-button" class="btn btn-success vpdk-enable-plugin" data-root="' . htmlspecialchars(JUri::base(true)) . '" data-extensionid="' . $extension_id . '" data-token="' . JSession::getFormToken() . '">Enable Plugin<span class="vpk-btn-overlay"></span></button>';
			}
			
			$html[] = '<div class="vpdk-info-box">';
			$html[] = '<div class="vpdk-info-default">';
			$html[] = '<p><strong>Oh snap!</strong> Enable this plugin first to change its default settings.</p>';
			$html[] = '</div>';
			$html[] = '</div>';
			
			JError::raiseWarning(100, 'Enable this plugin first to change its default settings.');
		}
		elseif(!$adapter)
		{
			$html[] = '<div class="vpdk-info-box">';
			$html[] = '<div class="vpdk-info-default">';
			$html[] = '<p><strong>Oh snap!</strong> Please enable <b>cURL</b> or turn-on <b>allow_url_fopen</b> to change default settings.</p>';
			$html[] = '</div>';
			$html[] = '</div>';
			
			$message = '<b>cURL</b> and <b>allow_url_fopen</b> both of these core PHP functions are disabled in your server. Please enable at least one of them to use this extension.';
			JError::raiseWarning(100, $message);
		}
		else
		{
			$html[] = '<button type="button" id="' . $this->id . '-button" class="btn' . $btn_class . '" data-vpdktarget="' . $this->id . '-tmpl-modal">';
			$html[] = '<span class="vpdk-add-text">Add</span>';
			$html[] = '<span class="vpdk-edit-text">Edit</span>';
			$html[] = '</button>';
			$html[] = '<button type="button" id="' . $this->id . '-clear-button" class="btn' . $clear_class . '">Clear</button>';
			$html[] = '<button type="button" id="' . $this->id . '-reval-button" class="btn btn-primary vpdk-reval-button' . $clear_class . '"' . $revalidate . '>Revalidate<span class="vpk-btn-overlay"></span></button>';
			$html[] = '<div class="vpdk-info-box">';
			$html[] = '<div class="vpdk-info-noaccess' . $access_class . '">';
			$html[] = '<p>You don\'t have access to the latest updates for this ' . $app->input->getCmd('view', 'extension') . '. Your subscription may have expired or you may not have a valid subscription for this item. To learn more about your subscription plan please visit your <a href="" target="_blank">Dashboard</a>.</p>';
			
			if($last_checked != $today)
			{
				$html[] = '<small>Key verified on: ' . JHtml::_('date', $last_checked, 'F d, Y H:i:s') . '</small>';
			}
			
			$html[] = '</div>';
			$html[] = '<div class="vpdk-info-valid' . $valid_class . '">';
			$html[] = '<p>Your subscription is active and you also have access to live updates.</p>';
			
			if($last_checked != $today)
			{
				$html[] = '<small>Key verified on: ' . JHtml::_('date', $last_checked, 'F d, Y H:i:s') . '</small>';
			}
			
			$html[] = '</div>';
			$html[] = '<div class="vpdk-info-default' . $def_class . '">';
			$html[] = '<p>Please add your Download Key above. <a href="https://www.virtueplanet.com/download-key/" target="_blank">Forgot Download Key?</a></p>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = $this->getFormTemplate($this->id . '-tmpl');
		}

		return implode(PHP_EOL, $html);
	}
	
	protected function getFormTemplate($id, $html = array())
	{
		$plugin = isset($this->element['plugin']) && !empty($this->element['plugin']) ? $this->element['plugin'] : 'vpinstaller';
		$extension_id = JFactory::getApplication()->input->getInt('extension_id', 0);
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$url = JRoute::_('index.php?option=com_ajax&plugin=' . $plugin . '&format=json&' . JSession::getFormToken() . '=1&extension_id=' . $extension_id);
		}
		else
		{
			$url = JRoute::_('index.php?plugin=' . $plugin . '&format=json&' . JSession::getFormToken() . '=1&extension_id=' . $extension_id);
		}
		
		$html[] = '<script id="' . $id . '" type="text/x-jquery-tmpl">';
		$html[] = '<div id="' . $id . '-modal" class="vpdk-modal">';
		$html[] = '<div class="vpdk-modal-dialog">';
		$html[] = '<div class="vpdk-modal-inner">';
		$html[] = '<form data-action="' . $url . '">';
		$html[] = '<div class="vpdk-modal-header">';
		$html[] = '<button type="button" class="vpdk-modal-close" title="Close">';
		$html[] = '<span aria-hidden="true">&times;</span>';
		$html[] = '</button>';
		$html[] = '<h4>Validate &amp Add Download Key</h4>';
		$html[] = '</div>';
		$html[] = '<div class="vpdk-modal-body">';
		$html[] = '<div class="vpdk-modal-message"></div>';
		$html[] = '<div class="vpdk-form-group">';
		$html[] = '<label for="' . $id . '-uname">VirtuePlanet Username</label>';
		$html[] = '<input type="text" id="' . $id . '-uname" name="uname" class="vpdk-form-control" placeholder="Username" autocomplete="off" spellcheck="false" required />';
		$html[] = '</div>';
		$html[] = '<div class="vpdk-form-group">';
		$html[] = '<label for="' . $id . '-dlk">Download Key</label>';
		$html[] = '<input type="text" id="' . $id . '-dlk" name="dlk" class="vpdk-form-control" placeholder="Download Key" autocomplete="off" spellcheck="false" required />';
		$html[] = '</div>';
		$html[] = '<div class="vpdk-modal-buttons">';
		$html[] = '<button type="button" id="' . $id . '-button" class="vpdk-btn vpdk-btn-lg vpdk-btn-primary"><i class="vpdk-button-loading"></i>Submit</button>';
		$html[] = '<button type="reset" id="' . $id . '-cancel" class="vpdk-btn vpdk-btn-lg vpdk-btn-default">Cancel</button>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<div class="vpdk-modal-footer">';
		$html[] = '<ul>';
		$html[] = '<li><a href="https://www.virtueplanet.com/lost-user-name/" target="_blank">Forgot Username?</a></li>';
		$html[] = '<li><a href="https://www.virtueplanet.com/download-key/" target="_blank">Forgot Download Key?</a></li>';
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = '</form>';
		$html[] = '</div>';
		$html[] = '<div class="vpdk-modal-inner-bg"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</script>';
		
		return implode(PHP_EOL, $html);
	}
	
	protected function isEnabled()
	{
		$app          = JFactory::getApplication();
		$option       = version_compare(JVERSION, '3.0.0', 'ge') ?
		                $app->input->getCmd('option', '') :
		                JRequest::getCmd('option', '');
		$view         = version_compare(JVERSION, '3.0.0', 'ge') ?
		                $app->input->getCmd('view', '') :
		                JRequest::getCmd('view', '');
		$extension_id = version_compare(JVERSION, '3.0.0', 'ge') ?
		                $app->input->getInt('extension_id', 0) :
		                JRequest::getInt('extension_id', 0);
		                
		if($option != 'com_plugins' && $view != 'plugin')
		{
			return true;
		}
		
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
		            ->select('name')
		            ->from('#__extensions')
		            ->where('extension_id = ' . (int) $extension_id)
		            ->where('enabled = 1');
		$db->setQuery($query);
		$result = $db->loadResult();
		
		$result = empty($result) ? false : $result;
		
		return $result;
	}
}