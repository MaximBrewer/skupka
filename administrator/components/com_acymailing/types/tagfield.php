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

class tagfieldType extends acymailingClass{
	var $onclick = '';

	function __construct(){
		parent::__construct();
	}

	function display($map, $mode = 'listing', $selectedTags = array()){
		$script = 'var selectedTags = [];
			function deletetag(a, option, value, tagid){
				option.parentNode.removeChild(option);
				a.parentNode.parentNode.removeChild(a.parentNode);

				var i = selectedTags.indexOf(value.toLowerCase());
				if(i != -1) {
					selectedTags.splice(i, 1);
				}
			}

			function addtag(value, tagid){
				var searchbartag = document.getElementById("searchbartag");
				searchbartag.value = "";
				document.getElementById("existingtags").style.display = "none";

				if(selectedTags.indexOf(value.toLowerCase()) != -1){
					return false;
				}
				
				var alltags = document.getElementById("existingtags").getElementsByTagName("li");
				for(var i = 0; i < alltags.length ; i++){
					if(alltags[i].innerHTML.toLowerCase() == value.toLowerCase()){
						value = alltags[i].innerHTML;
						break;
					}
				}
				
				selectedTags.push(value.toLowerCase());

				var option = document.createElement("option");
				option.text = value;';
		
		if(is_object($mode)) {
			$script .= 'option.value = value;';
		}elseif($mode == 'listing') {
			$script .= 'option.value = tagid+"|"+value;';
		}elseif($mode == 'tags') {
			$script .= 'option.value = tagid;';
		}

		$script .= 'option.selected = "selected";

				var select = document.getElementById("tagselect");
				select.add(option);


				var li = document.createElement("li");
				li.className = "tagchoice";

				var span = document.createElement("span");
				span.innerHTML = value.replace(/</g, "&lt;");
				li.appendChild(span);

				var a = document.createElement("a");
				a.className = "choice-close";
				a.onclick = function(){
					deletetag(this, option, value);';
		$script .= $this->onclick.'};
				li.appendChild(a);

				searchbartag.parentNode.parentNode.insertBefore(li, searchbartag.parentNode);';
		if(!is_object($mode)) $script .= 'searchbartag.placeholder = "";searchbartag.style.width = "60px";';
		$script .= '}

			function myKeyPress(e, textbar){';

		if(is_object($mode)){
			$script .= 'var keynum;
				if(window.event) {
				  keynum = e.keyCode;
				}else if(e.which){
				  keynum = e.which;
				}

				if(textbar.value.length >= 3 && (keynum == 13 || keynum == 188)){
					if(keynum == 188){
						textbar.value = textbar.value.substring(0, textbar.value.length - 1);
					}
					addtag(textbar.value, 0);
					return false;
				}';
		}

		$script .= 'if(textbar.value.length >= 3){
					displayPropositions(textbar.value);
				}else{
					document.getElementById("existingtags").style.display = "none";
				}

				return true;
			}
			
			function displayPropositions(value){
				var found = false;

				var propsdiv = document.getElementById("existingtags");
				propsdiv.style.display = "block";

				var alltags = propsdiv.getElementsByTagName("li");
				for(var i = 0; i < alltags.length ; i++){
					if(alltags[i].innerHTML.toLowerCase().indexOf(value.toLowerCase()) !== -1){
						found = true;
						alltags[i].style.display = "block";
					}else{
						alltags[i].style.display = "none";
					}
				}

				if(!found) propsdiv.style.display = "none";
			}
			
			function validateBlur(input){
				setTimeout(function(){
					if(input.value.length >= 3){
						addtag(input.value, 0);
					}
				},200);
			}';

		if(!is_object($mode)){
			if(ACYMAILING_J30){
				$script .= ' function removeChosen(){
							jQuery("#tagfilter .chzn-container").remove();
							jQuery("#tagfilter .chzn-done").removeClass("chzn-done");
						}';
			}
			$script .= 'document.addEventListener("DOMContentLoaded", function(){';

			if(ACYMAILING_J30){
				$script .= 'removeChosen();
					setTimeout(function(){
						removeChosen();
					}, 100);';
			}

			if(!empty($selectedTags)){
				foreach($selectedTags as $oneTag){
					if(empty($oneTag)) continue;
					$tag = explode('|', $oneTag);
					$script .= 'addtag("'.str_replace('"', '\"', $tag[1]).'", '.$tag[0].');';
				}
			}

			$script .= '});';
		}

		if(!empty($mode->mailid)){
			$tags = acymailing_loadResultArray('SELECT a.name FROM #__acymailing_tag AS a JOIN #__acymailing_tagmail AS b ON a.tagid = b.tagid WHERE b.mailid = '.intval($mode->mailid).' ORDER BY name ASC');

			if(!empty($tags)){
				$script .= 'document.addEventListener("DOMContentLoaded", function(){';
				foreach($tags as $oneTag){
					$script .= 'addtag("'.str_replace('"', '\"', $oneTag).'", 0);';
				}
				$script .= '});';
			}
		}

		acymailing_addScript(true, $script);

		if(is_object($mode)){
			$result = $this->_inputDisplay($map);
		}else{
			$result = $this->_filterDisplay($map);
		}

		return $result;
	}

	function _filterDisplay($map){
		ob_start();
		?>

		<div id="tagfilter" style="display: inline-block;">
			<select id="tagselect" name="filter_tags[]" multiple="multiple" style="display: none;">
				<option value="" selected="selected"></option>
			</select>

			<div id="tagfieldcontainer">
				<ul id="tagul" onclick="document.getElementById('searchbartag').focus();">
					<li class="searchtag"><input autocomplete="off" id="searchbartag" placeholder="<?php echo acymailing_translation('ACY_TYPE_SOMETHING'); ?>" type="text" onkeydown="if(event.keyCode == 13){return false}" onkeyup="return myKeyPress(event, this);"/></li>
				</ul>
				<div id="existingtags" style="display: none;">
					<ul id="existingtagsul">
						<?php
						$allTags = acymailing_loadObjectList('SELECT tagid, name FROM #__acymailing_tag ORDER BY name ASC LIMIT 500');

						if(!empty($allTags)){
							foreach($allTags as $oneTag){
								echo '<li onclick="addtag(\''.str_replace(array("'", '"'), array("&rsquo;", '&quot;'), $oneTag->name).'\', '.$oneTag->tagid.');'.$this->onclick.'">'.str_replace("<", "&lt;", $oneTag->name).'</li>';
							}
						}
						?>
					</ul>
				</div>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	function _inputDisplay($map){

		ob_start();
		?>
		<select id="tagselect" name="<?php echo $map; ?>[]" multiple="multiple" style="display: none;">
		</select>

		<div id="tagfieldcontainer">
			<ul id="tagul" onclick="document.getElementById('searchbartag').focus();">
				<li class="searchtag"><input onblur="validateBlur(this);" autocomplete="off" id="searchbartag" placeholder="<?php echo acymailing_translation('ACY_TYPE_SOMETHING'); ?>" type="text" onkeyup="return myKeyPress(event, this);"/></li>
			</ul>
			<div id="existingtags" style="display: none;">
				<ul id="existingtagsul">
					<?php
						$allTags = acymailing_loadResultArray('SELECT name FROM #__acymailing_tag ORDER BY name ASC LIMIT 500');

						if(!empty($allTags)){
							foreach($allTags as $oneTag){
								echo '<li onclick="addtag(\''.htmlspecialchars($oneTag, ENT_QUOTES, "UTF-8").'\', 0);">'.htmlspecialchars($oneTag, ENT_QUOTES, "UTF-8").'</li>';
							}
						}
					?>
				</ul>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}
}
