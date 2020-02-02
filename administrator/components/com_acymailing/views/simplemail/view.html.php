<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class SimpleMailViewSimpleMail extends acymailingView{

	public function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();
		parent::display($tpl);
	}

	public function edit(){
		$toolbar = acymailing_get('helper.toolbar');
		$toolbar->setTitle(acymailing_translation('SIMPLE_SENDING'), 'simplemail&task=edit');
		$toolbar->popup('template', acymailing_translation('ACY_TEMPLATE'), acymailing_completeLink("template&task=theme", true));
		$toolbar->popup('tag', acymailing_translation('TAGS'), acymailing_completeLink("tag&task=tag&type=news", true));
		$toolbar->divider();
		$toolbar->custom('send', acymailing_translation('SEND'), 'send', false);
		$toolbar->divider();
		$toolbar->help('simplesending#edit');
		$toolbar->display();
		$this->testreceiverType = acymailing_get('type.testreceiver');
		$infos = new stdClass();
		$infos->test_selection = $infos->test_group = $infos->test_emails = '';
		$infos->test_html = '';
		$this->infos = $infos;

		acymailing_session();
		$this->tempid = empty($_SESSION['acymailing']['simplesending_tempid']) ? 0 : $_SESSION['acymailing']['simplesending_tempid'];
		$_SESSION['acymailing']['simplesending_tempid'] = 0;
		$this->subject = empty($_SESSION['acymailing']['simplesending_subject']) ? '' : $_SESSION['acymailing']['simplesending_subject'];
		$_SESSION['acymailing']['simplesending_subject'] = '';
		$content = empty($_SESSION['acymailing']['simplesending_body']) ? '' : $_SESSION['acymailing']['simplesending_body'];
		$_SESSION['acymailing']['simplesending_body'] = '';

		$editor = acymailing_get('helper.editor');
		$editor->name = 'editor_body';
		$editor->content = $content;
		if($this->tempid > 0){
			$editor->setTemplate($this->tempid);
			$editor->setEditorStylesheet($this->tempid);
		}
		$this->editor = $editor;
		$this->insertScript($editor);
		$this->insertCSS();
	}

	private function insertScript($editor){
		$script = '';
		$script .= "function changeTemplate(newhtml,newtext,newsubject,stylesheet,fromname,fromemail,replyname,replyemail,tempid){
			if(newhtml.length>2){".$editor->setContent('newhtml')."}
			var vartextarea = document.getElementById('altbody');
			if(newtext.length>2) vartextarea.innerHTML = newtext;
			document.getElementById('tempid').value = tempid;
			if(fromname.length>1){
				fromname = fromname.replace('&amp;', '&');
				document.getElementById('fromname').value = fromname;
			}
			if(fromemail.length>1){document.getElementById('fromemail').value = fromemail;}
			if(replyname.length>1){
				replyname = replyname.replace('&amp;', '&');
				document.getElementById('replyname').value = replyname;
			}
			if(replyemail.length>1){document.getElementById('replyemail').value = replyemail;}
			if(newsubject.length>1){
				newsubject = newsubject.replace('&amp;', '&');
				var subjectObj = document.getElementById('subject');
				if(subjectObj.tagName.toLowerCase() == 'input'){
					subjectObj.value = newsubject;
				}else{
				    subjectObj.innerHTML = newsubject;
				}
			}
			".$editor->setEditorStylesheet('tempid')."
		}
		";

		$script .= "var zoneEditor = 'editor_body';
			var zoneToTag = 'editor';
			document.addEventListener('DOMContentLoaded', function(){
				setTimeout(function() {
					document.getElementById('editor_body_ifr').addEventListener('click', function(){
						zoneToTag = 'editor';
					});	
					
					var ediframe = document.getElementById('editor_body_ifr').getElementsByTagName('iframe');
					if(ediframe && ediframe[0]){
						var children = ediframe[0].contentDocument.getElementsByTagName('*');
						for (var i = 0; i < children.length; i++) {
							children[i].addEventListener('click', function(){
								zoneToTag = 'editor';
							});			
						}
					}		
				}, 1000);
			});";
		$script .= "var previousSelection = false;
			function insertTag(tag){
				if(zoneEditor == 'editor_body' && zoneToTag == 'editor'){
					try{
						jInsertEditorText(tag,'editor_body',previousSelection);
						return true;
					} catch(err){
						alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter');
						return false;
					}
				} else{
					try{
						simpleInsert(zoneToTag, tag);
						return true;
					} catch(err){
						alert('Error inserting the tag in the '+ zoneToTag + 'zone. Please copy/paste it manually in your Newsletter.');
						return false;
					}
				}
			}
			";
		$script .= "function simpleInsert(myField, myValue) {
				myField = document.getElementById(myField);
				if (document.selection) {
					myField.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
				} else if (myField.selectionStart || myField.selectionStart == '0') {
					var startPos = myField.selectionStart;
					var endPos = myField.selectionEnd;
					myField.value = myField.value.substring(0, startPos)
						+ myValue
						+ myField.value.substring(endPos, myField.value.length);
				} else if (myField.tagName == 'DIV') {
					myField.innerHTML += myValue;
					document.getElementById('subject').value += myValue;
				} else {
					myField.value += myValue;
				}
			}";
		$script .= '
		window.addEventListener("load", function(event) {
			var sendButton = document.getElementById("toolbar-send");
			var onClick = sendButton.onclick;
			sendButton.onclick = "";
			sendButton.addEventListener("click", function() { var val = document.getElementById("message_receivers").value; if(val != ""){ setUser(val); } onClick()});
			var emailField = document.getElementById("message_receivers");
			emailField.addEventListener("keypress", function(event) {var char = event.which || event.keyCode; if(this.value != "" && char == 13) {setUser(this.value)}});
		});
		';
		acymailing_addScript(true, $script);
		$installedPlugin = acymailing_getPlugin('acymailing', 'emojis');
		if(!empty($installedPlugin)){
			$params = new acyParameter($installedPlugin->params);
			if(acymailing_isPluginEnabled('acymailing', 'emojis') && $params->get('subject', 1) == 1) {
				if (!ACYMAILING_J30) {
					acymailing_addScript(false, ACYMAILING_JS . 'jquery/jquery-1.9.1.min.js?v=' . filemtime(ACYMAILING_ROOT . 'media' . DS . 'com_acymailing' . DS . 'js' . DS . 'jquery' . DS . 'jquery-1.9.1.min.js'));
					acymailing_addScript(false, ACYMAILING_JS . 'jquery/jquery-ui.min.js?v=' . filemtime(ACYMAILING_ROOT . 'media' . DS . 'com_acymailing' . DS . 'js' . DS . 'jquery' . DS . 'jquery-ui.min.js'));
				}
				acymailing_addScript(false, acymailing_rootURI().'plugins/editors/acyeditor/acyeditor/ckeditor/plugins/smiley/emojionearea.js?v=' . filemtime(ACYMAILING_ROOT . 'plugins' . DS . 'editors' . DS . 'acyeditor' . DS . 'acyeditor' . DS . 'ckeditor' . DS . 'plugins' . DS . 'smiley' . DS . 'emojionearea.js'));
				acymailing_addScript(false, acymailing_rootURI().'plugins/editors/acyeditor/acyeditor/ckeditor/plugins/smiley/dialogs/emojimap.js?v=' . filemtime(ACYMAILING_ROOT . 'plugins' . DS . 'editors' . DS . 'acyeditor' . DS . 'acyeditor' . DS . 'ckeditor' . DS . 'plugins' . DS . 'smiley' . DS . 'dialogs' . DS . 'emojimap.js'));
				acymailing_addStyle(false, acymailing_rootURI().'plugins/editors/acyeditor/acyeditor/ckeditor/plugins/smiley/emojionearea.css?v=' . filemtime(ACYMAILING_ROOT . 'plugins' . DS . 'editors' . DS . 'acyeditor' . DS . 'acyeditor' . DS . 'ckeditor' . DS . 'plugins' . DS . 'smiley' . DS . 'emojionearea.css'));

				acymailing_addScript(true, '
					jQuery(document).ready(function() {
						jQuery("#subject").emojioneArea({
							pickerPosition: "bottom",
							shortnames: true
						});
					});
				');
			}
		}
	}

	private function insertCSS(){
		$css = '
			#test_selection_chzn, #test_selection {
				display: none !important;
			}

			.mail-part input[type="text"] {
				width: 99%;
			}

			#usersSelected {
				display: inline !important;
			}

			#message_receivers {
				margin: 0 25px 0 0 !important;
			}

			.mail-part {
				width: 100%;
				margin: 15px auto;
				background: white;
				padding: 15px;
			}

			#subject {
				margin: 15px 0 35px 0;
			}

			#message_receivers {
				width: 40% !important;
			}

			.mail-information {
				width: 80%;
				display: block;
				margin: 0 auto;
			}

			.mail-information:after{
				content: "";
				display: block;
				clear: both;
			}

		';

		acymailing_addStyle(true, $css);
	}
}
