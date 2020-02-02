<?php
defined('_JEXEC') or die;
JLoader::register('EditorCustomHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/editor.php');

abstract class JHtmlEditorCustom{
	static public function write($name, $id, $value) {
		switch (JComponentHelper::getParams('com_yandex_maps')->get('use_editor', 1)) {
			case 0:
				$html = '<textarea rows="10" class="span12 properties" name="'.$name.'" id="'.$id.'">'.htmlspecialchars($value).'</textarea>';
				$html.= "<script>
					Joomla.editors.instances['".$id."'] = jQuery('#".$id."');
					window.jInsertEditorText = function(tag, editor) {
						Joomla.editors.instances[editor].val(Joomla.editors.instances[editor].val()+tag);
					}
				</script>";
			break;
			case 2:
				$editor = JFactory::getEditor();
				$html = $editor->display($name, $value, '550', '400', '60', '20', true);
				$html.= "<script>
					jQuery('#".$name."').on('update', function() {
						".$editor->setContent($name,'jQuery(this).val()')."
					});
					setInterval(function () {
						var content = ".$editor->getContent($name).";
						if (content!=jQuery('#".$name."').val()) {
							jQuery('#".$name."').val(content).trigger('changex');
						}
					}, 500);
					Joomla.submitbutton = function(task) {
						".$editor->save($name).";
						Joomla.submitform(task, document.getElementById(\"adminForm\"));
					};
				</script>";
			break;
			case 1:
				$doc = JFactory::getDocument();
				$doc->addScript(JURI::root().'media/com_yandex_maps/js/wysiwyg/ckeditor/ckeditor.js');
				$html = '<textarea class="span12 properties" name="'.$name.'" id="'.$id.'">'.htmlspecialchars($value).'</textarea>';
				$html.= "<script>
					Joomla.editors.instances['".$id."'] = CKEDITOR.replace('{$id}', {
						removeButtons:'scayt,Underline,Subscript,Superscript',
						baseHref:'".JURI::root()."',
						disableNativeSpellChecker:false,
						allowedContent:true,
						language:'ru'
					});
					window.jInsertEditorText = function(tag, editor) {
						Joomla.editors.instances[editor].insertHtml(tag);
					}
					Joomla.editors.instances['".$id."'].on('change', function() {
						this.updateElement()
						jQuery('#".$id."').trigger('change');
					});
					jQuery('#".$id."').on('update', function() {
						Joomla.editors.instances['".$id."'].setData(jQuery(this).val());
					});
				</script>";
			break;
			case 3: 
				$html = '<textarea class="span12 properties" name="'.$name.'" id="'.$id.'">'.htmlspecialchars($value).'</textarea>';
				JHtml::stylesheet(JURI::root() . 'media/com_yandex_maps/js/wysiwyg/jquery.cleditor.css', array(), true);
				JHtml::script(JURI::root() . 'media/com_yandex_maps/js/wysiwyg/jquery.cleditor.js');
				$html.= "<script>
							window.jInsertEditorText = function(tag, editor) {
								Joomla.editors.instances[editor][0].focus();
								Joomla.editors.instances[editor][0].execCommand('inserthtml', tag, null, 0);
							}
							if (jQuery.fn.cleditor) {
								jQuery.cleditor.defaultOptions.docExternalHead = '<base href=\"".JURI::root()."\">';
								var editor = jQuery('#".$id."').cleditor();
								editor.change(function(){
									this.updateTextArea();
									this.\$area.trigger('change');
								});
								jQuery('#".$id."').on('update', function() {
									editor[0].updateFrame();
								});
								Joomla.editors.instances['".$id."'] = editor;
							}
						</script>";
			break;
		}
		if (JComponentHelper::getParams('com_yandex_maps')->get('use_editor')!==2) {
			JHTML::_( 'behavior.modal' );	
			$html.= "<script>
					jQuery(function($) {
						if (!SqueezeBox.doc) {
							SqueezeBox.initialize({});
							SqueezeBox.assign($('a.modal').get(), {
								parse: 'rel'
							});
						}
					});
					function jModalClose() {
						SqueezeBox.close();
					}
					function insertReadmore(editor){
						var content = jQuery('#'+editor).val();
						if (content.match(/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i)) {
							alert('".JText::_('COM_YANDEX_MAPS_READMORE_ALREADY_EXISTS')."');
							return false;
						} else {
							jInsertEditorText('<hr id=\"system-readmore\" />', editor);
						}
					}
		</script>";
				$html.= '<div id="editor-xtd-buttons" class="btn-toolbar pull-left">
							<a class="btn modal" title="Изображение"  rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="'.JURI::root().'administrator/index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name='.$id.'&amp;asset=61&amp;author='.JFactory::getUser()->id.'" onclick="return false;"><i class="icon-picture"></i> Изображение</a>
							<a class="btn" title="Подробнее..." href="javascript:void(0)" onclick="insertReadmore(\''.$id.'\');return false;" rel=""><i class="icon-arrow-down"></i> Подробнее...</a>
						</div>';
		}
		return $html;
	}
}
