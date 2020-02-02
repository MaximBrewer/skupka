<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.text');

class NextendElementImage extends NextendElementText {

    function fetchElement() {
        $html = parent::fetchElement();
        if (nextendIsJoomla()) {
            JHtml::_('behavior.modal');
            $user = JFactory::getUser();
            $link = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name=com_smartslider2&amp;author=' . $user->id;
            $html .= '<div class="button2-left" style="margin: 2px 0 2px 10px; float: left;">
                    <div class="image">
                        <a onclick="window.jInsertEditorText = function(tag, editor){njQuery(\'#' . $this->_id . '\').val((window.nextendimageurl == 0 ? \'' . NextendUri::getBaseUri() . '\' : \'\')+njQuery(tag).attr(\'src\')); NfireEvent(document.getElementById(\'' . $this->_id . '\'),\'change\'); };return false;" rel="{handler: \'iframe\', size: {x: 900, y: 520}}" href="' . $link . '" title="Image" class="modal btn modal-button"><i class="icon-picture"></i>'.NextendText::_('Image').'</a>
                    </div>
                  </div>';
        }else if (nextendIsWordPress()) {
            add_thickbox();
			      wp_enqueue_script('media-upload');
            wp_print_styles('editor-buttons'); 
            $html .= '
              <div class="wp-media-buttons" style="float: left; margint-top:1px;">
                <a title="Add Media" onclick="window.send_to_editor = function(html){var inp = njQuery(\'#'.$this->_id.'\'); setFromWPLibrary(html, inp); };tb_show(\'\', \''.admin_url('media-upload.php?type=image&TB_iframe=true&width=800&height=800').'\');return false;" data-editor="content" class="button insert-media add_media" href="#"><span class="wp-media-buttons-icon"></span> Add Media</a>
              </div>
              <script>
                  function setFromWPLibrary(html, el){
                      if( Object.prototype.toString.call( html) === \'[object Array]\' ) {
                          el.val(html[0]);
                      }else{
                          var html = jQuery(html);
                          var img = html;
                          if(html[0].tagName != \'IMG\'){
                        		img = jQuery(\'img\',html);
                      		}
                      		el.val(img.attr(\'src\'));
                      }
                      NfireEvent(el[0], \'change\');
                      
                  		tb_remove();
                  }
              </script>
              ';
        }else if (nextendIsMagento()) {
            static $loaded;
            ob_start();
            if($loaded == null){
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'extjs/ext-tree.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'extjs/ext-tree-checkbox.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'extjs/fix-defer.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'mage/adminhtml/variables.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'mage/adminhtml/wysiwyg/widget.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'lib/flex.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'lib/FABridge.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'mage/adminhtml/flexuploader.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'mage/adminhtml/browser.js"></script>';
                $html.= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'prototype/window.js"></script>';
                
                $html.= '<link rel="stylesheet" type="text/css" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'prototype/windows/themes/default.css" media="screen"/>';
                $html.= '<link rel="stylesheet" type="text/css" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/lib/prototype/windows/themes/magento.css" media="screen"/>';
            ?>
<script type="text/javascript">
njQuery(window).ready(function(){
Mediabrowser.prototype.insert = Mediabrowser.prototype.insert.wrap(function(parentMethod, event) {
    var div;
    if (event != undefined) {
        div = $(Event.findElement(event, 'DIV'));
    } else {
        $$('div.selected').each(function (e) {
            div = jQuery('#'+e.id);
        });
    }
    if (div.length == 0) {
        return false;
    }
    var targetEl = this.getTargetElement();
    if (! targetEl) {
        alert("Target element not found for content update");
        Windows.close('browser_window');
        return;
    }
    div = div[0];
    var params = {filename:div.id, node:this.currentNode.id, store:this.storeId};

    if (targetEl.tagName.toLowerCase() == 'textarea') {
        params.as_is = 1;
    }

    new Ajax.Request(this.onInsertUrl, {
        parameters: params,
        onSuccess: function(transport) {
            try {
                this.onAjaxSuccess(transport);
                if (this.getMediaBrowserOpener()) {
                    self.blur();
                }
                Windows.close('browser_window');
                if (targetEl.tagName.toLowerCase() == 'input') {
                    targetEl.value = transport.responseText;
                    NfireEvent(targetEl, 'change');
                    // AntoineK overload START
                    if (varienGlobalEvents) {
                        varienGlobalEvents.fireEvent('mediachooserChange' + targetEl.id, transport.responseText);
                    }
                    // AntoineK overload END

                } else {
                    updateElementAtCursor(targetEl, transport.responseText);
                    if (varienGlobalEvents) {
                        varienGlobalEvents.fireEvent('tinymceChange');
                    }
                }
            } catch (e) {
                alert(e.message);
            }
        }.bind(this)
    });
});
});
</script>
<?php
$loaded = true;
}
?>
<script type="text/javascript">
    njQuery(window).ready(function(){
        jQuery('#<?php echo $this->_id; ?>_button').on('click', function(event){
            event.preventDefault();
            MediabrowserUtility.openDialog('<?php echo Mage::helper("adminhtml")->getUrl('adminhtml/cms_wysiwyg_images_chooser/index', array('target_element_id' => $this->_id, 'store' => 'undefined', 'type' => 'image')); ?>');
        });
    });
</script>
            <?php
            $html.= ob_get_clean();
            $html.= '<a id="'.$this->_id.'_button" title="Add Image" data-editor="content" class="button add-image" href="#">
                <span class="wp-media-buttons-icon"></span> Add Image
            </a>';
        }
        return $html;
    }
}
