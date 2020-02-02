<?php
/*--------------------------------------------------------------------------------------------------------
# VP One Page Checkout - Joomla! System Plugin for VirtueMart 3
----------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012-2017 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      https://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 3 $
$LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
$Id: vpadminhelper.php 3 2017-09-20 14:30:08Z Abhshek Das $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldVPAdminHelper extends JFormField
{
	
	public $type = 'VPAdminHelper';
	
	protected function getInput()
	{
		$app = JFactory::getApplication();
		
		if(!class_exists('VmConfig'))
		{
			$file = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
			if(file_exists($file)) require($file);
		}
		
		if(!defined('VM_VERSION'))
		{
			$app->enqueueMessage('It appears VirtueMart Component is not installed. VP One Page Checkout plugin is an extension of VirtueMart Component.', 'error');
			return;
		}
		
		if(VM_VERSION < 3)
		{
			$app->enqueueMessage('This package of VP One Page Checkout plugin is compatible to VirtueMart 3 and above. You can get VirtueMart ' . VM_VERSION . 
			                     ' compatible package of the plugin from http://www.virtueplanet.com', 'error');
		}
		
		$doc = JFactory::getDocument();
		$root = JUri::root(true);
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$staticJS = 'plugins/system/vponepagecheckout/assets/admin/js/j3/admin.js';
			$staticCSS = 'plugins/system/vponepagecheckout/assets/admin/css/j3/admin.css';
		}
		else
		{
			$staticJS = array();
			$staticJS[] = 'plugins/system/vponepagecheckout/assets/admin/js/jquery.min.js';
			$staticJS[] = 'plugins/system/vponepagecheckout/assets/admin/js/jquery-noconflict.js';
			$staticJS[] = 'plugins/system/vponepagecheckout/assets/admin/js/jquery-migrate.min.js';
			$staticJS[] = 'plugins/system/vponepagecheckout/assets/admin/js/j2.5/admin.js';
			
			$staticCSS = 'plugins/system/vponepagecheckout/assets/admin/css/j2.5/admin.css';
		}
		
		$scripts = !empty($this->element['scripts']) ? $this->element['scripts'] : $staticJS;
		$styleSheets = !empty($this->element['styleSheets']) ? $this->element['styleSheets'] : $staticCSS;
		
		if(!empty($scripts))
		{
			if(is_string($scripts))
			{
				if(strpos($scripts, ',') !== false)
				{
					$scripts = explode(',', $scripts);
				}
				else
				{
					$scripts = (array) $scripts;
				}
			}
			
			foreach($scripts as $script)
			{
				$path = JPath::clean(JPATH_ROOT . '/' . $script);
				
				if(file_exists($path))
				{
					$version = strpos($script, 'j2.5') || strpos($script, 'j3') ? '?ver=3.2' : '';
					$doc->addScript($root . '/' . trim($script) . $version);
				}
			}
		}
		
		if(!empty($styleSheets))
		{
			if(is_string($styleSheets))
			{
				if(strpos($styleSheets, ',') !== false)
				{
					$styleSheets = explode(',', $styleSheets);
				}
				else
				{
					$styleSheets = (array) $styleSheets;
				}
			}
			
			foreach($styleSheets as $styleSheet)
			{
				$path = JPath::clean(JPATH_ROOT . '/' . $styleSheet);
				
				if(file_exists($path))
				{
					$version = strpos($styleSheet, 'j2.5') || strpos($styleSheet, 'j3') ? '?ver=3.2' : '';
					$doc->addStyleSheet($root . '/' . trim($styleSheet) . $version);
				}
			}
		}
		
		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$js = "
			jQuery(document).ready(function($) {
				var elementGroup = $('#general input#jform_element:text').parents('.control-group');
				if(elementGroup.length) {
					var versionGroup = elementGroup.clone();
					versionGroup.find('label').attr('id', 'jform_version-lbl').attr('for', 'jform_version').text('Plugin Version').removeAttr('data-original-title').attr('title', '<strong>Plugin Version</strong><br/>VP One Page Checkout plugin version installed in your site.');
					versionGroup.find('input:text').attr('id', 'jform_version').attr('name', 'jform[version]').attr('disabled', true).val('fetching...');
					elementGroup.after(versionGroup);
					var form = elementGroup.parents('form');
					versionField = $('#jform_version');
					versionGroup = versionField.parents('.control-group');
					$('.hasTooltip', versionGroup).tooltip({'html': true,'container': 'body'});
					$.ajax({
						type: 'GET',
						url: form.attr('action'),
						data: {'ctask' : 'getplgversion', '" . JSession::getFormToken() . "' : 1},
						success: function(e) {
							if($.type(e) === 'string' && e.indexOf('<\/head>') != -1) {
								versionGroup.addClass('warning');
								versionField.val('Enable the plugin to fetch version');
							}
							else if(e.error == 1) {
								versionGroup.remove();
							} else {
								versionGroup.addClass('success');
								versionField.val(e.version);
								$('.vp-extension-description .extension-version').text(e.version).addClass('label label-info');
							}
						}
					});
				}
			});
			";
		}
		else
		{
			$js = "
			jQuery(document).ready(function($) {
				var elementGroup = $('.adminformlist input#jform_extension_id:text').parent('li');
				if(elementGroup.length) {
					var versionGroup = elementGroup.clone();
					versionGroup.find('label').attr('id', 'jform_version-lbl').attr('for', 'jform_version').text('Plugin Version').removeAttr('data-original-title').attr('title', 'Plugin Version::VP One Page Checkout plugin version installed in your site.');
					versionGroup.find('input:text').attr('id', 'jform_version').attr('name', 'jform[version]').attr('disabled', true).val('fetching...');
					elementGroup.after(versionGroup);
					var form = elementGroup.parents('form');
					versionField = $('#jform_version');
					versionGroup = versionField.parents('li');
					$$('.hasTip').each(function(el) {
						var title = el.get('title');
						if (title) {
							var parts = title.split('::', 2);
							el.store('tip:title', parts[0]);
							el.store('tip:text', parts[1]);
						}
					});
					var JTooltips = new Tips($$('.hasTip'), {maxTitleChars: 50, fixed: false});
					$.ajax({
						type: 'GET',
						url: form.attr('action'),
						data: {'ctask' : 'getplgversion', '" . JSession::getFormToken() . "' : 1},
						success: function(e) {
							if($.type(e) === 'string' && e.indexOf('<\/head>') != -1) {
								versionField.val('Enable to plugin first').addClass('warning');
							}
							else if(e.error == 1) {
								versionGroup.remove();
							} else {
								versionField.addClass('success').val(e.version);
								$('.vp-extension-description .extension-version').text(e.version).addClass('label label-info');
							}
						},
						error: function() {
							versionField.val('Enable the plugin to fetch version');
						}
					});
				}
			});
			";
		}
		
		$doc->addScriptDeclaration($js);
		return null;
	}
	
	public function getLabel() 
	{
		return null;
	}
}