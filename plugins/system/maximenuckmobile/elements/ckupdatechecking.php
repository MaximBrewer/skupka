<?php

/**
 * @copyright	Copyright (C) 2015 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JFormFieldCkupdatechecking extends JFormField {

	protected $type = 'ckupdatechecking';

	protected $url = 'http://www.joomlack.fr/en/joomla-extension-maximenu/maximenu-mobile-plugin';

	protected $jsonurl = 'http://update.joomlack.fr/plg_maximenuckmobile_update.json?callback=?';

	private $txt_current_version, $txt_available_version, $txt_download;

	protected function getLabel() {

		$txt_current_version = JText::_('PLG_MAXIMENUCK_YOU_HAVE_VERSION');
		$txt_available_version = JText::_('PLG_MAXIMENUCK_NEW_VERSION_AVAILABLE');
		$txt_download = JText::_('PLG_MAXIMENUCK_DOWNLOAD');

		// get the version installed
		$installed_version = false;
		$file_url = JPATH_SITE .'/plugins/system/maximenuckmobile/maximenuckmobile.xml';
		if(! file_exists($file_url)) return '';
		// read the xml
		if (! $xml_installed = JFactory::getXML($file_url)) {
			die;
		} else {
			$installed_version = (string)$xml_installed->version;
		}

		$js_checking = '';
	
		$js_checking = '<script>
			jQuery(document).ready(function (){
				// check the release notes
				updateck = function() {}; // needed to avoid errors on bad request
				jQuery.ajax({
						type: "GET",
						url: "'.$this->jsonurl.'",
						jsonpCallback: "updateck",
						contentType: "application/json",
						dataType: "jsonp",
					}).done(function(response) {
						for (var version in response) {
							if (compareVersions(version,"' . $installed_version . '")) {
								if (! jQuery("#updatealert").text().length) {
									jQuery("#updatealert").append("<span class=\"label label-warning\" style=\"font-size:1em;padding:0.4em;\">' . $txt_available_version . '</span>");
									jQuery("#updatealert").append("<a href=\"'.$this->url.'\" target=\"_blank\" class=\"pull-right btn btn-info\" style=\"font-size:1em;padding:0.2em 0.4em;margin: 0 5px;\"><i class=\"icon icon-download\"></i>' . $txt_download . '</a>");
								}
								var notes = writeVersionInfo(response, version);
								jQuery(".updatechecking").append(notes);
							}
						}
					}).fail(function( jqxhr, textStatus, error ) {
						// var err = textStatus + ", " + error;
						// console.log( "Request Failed: " + err );
					});
				
			});
			
			function compareVersions(installed, required) {
				var a = installed.split(".");
				var b = required.split(".");

				for (var i = 0; i < a.length; ++i) {
					a[i] = Number(a[i]);
				}
				for (var i = 0; i < b.length; ++i) {
					b[i] = Number(b[i]);
				}
				if (a.length == 2) {
					a[2] = 0;
				}

				if (a[0] > b[0]) return true;
				if (a[0] < b[0]) return false;

				if (a[1] > b[1]) return true;
				if (a[1] < b[1]) return false;

				if (a[2] > b[2]) return true;
				if (a[2] < b[2]) return false;

				return false;
			}
			
			function writeVersionInfo(response, version) {
				var txt = "<div>";
				txt += "<strong class=\"badge\">Version : " + version + "</strong>";
				txt += " - Date : " + response[version]["date"];
				txt += "</div>";
				txt += "<ul>";
				for (i=0;i<response[version]["notes"].length;i++) {
					txt += "<li>" + response[version]["notes"][i] + "</li>";
				}
				txt += "</ul>";
				// txt += "<br />";
				return txt;
			}
		</script>';

		$html = '<style>.updatechecking { /*background:#efefef;*/
	border: none;
    border-radius: 3px;
    color: #333;
    font-weight: normal;
	line-height: 24px;
    padding: 5px;
	margin: 3px 0;
    text-align: left;
    text-decoration: none;
    }
	.updatechecking img {
	margin: 5px;
    }</style>';


		$html .= '<div>' . $txt_current_version . ' : <span class="label">' . $installed_version . '</span></div>';
		$html .= '<hr />';
		$html .= '<div id="updatealert"></div>';
		$html .= '<div class="updatechecking"></div>';

		$html .= $js_checking;
		return $html;
	}
	
	protected function getPathToElements() {
		$localpath = dirname(__FILE__);
		$rootpath = JPATH_ROOT;
		$httppath = trim(JURI::root(), "/");
		$path = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
		return $path;
    }

	protected function getInput() {

		return '';
	}
}

