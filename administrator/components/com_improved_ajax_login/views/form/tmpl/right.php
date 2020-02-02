<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined('_JEXEC') or die;
?>
<script>
(window.jq183||jQuery)(function($) {
  var origVal = $('#jform_language').on("change", function() {
    if (confirm("Are you sure you want to change to other language?\n(If you didn't save, your changes will lost)")) {
      location.href = location.href.replace('&id=', '&orig=')+'&lang='+$(this).val();
    } else {
      $(this).val(origVal);
    }
  }).val();
});
</script>
<ul class="nav nav-tabs">
  <li class="active"><a id="form-tab" href="#form-settings" data-toggle="tab"><i class="icon-cog"></i> Form</a></li>
  <li class="hidden"><a id="elem-tab" href="#elem-settings" data-toggle="tab"><i class="icon-wrench"></i> Field</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="form-settings">
    <form action="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&layout=edit&id=' .(int)$this->item->id) ?>"
      method="post" enctype="multipart/form-data" name="adminForm" id="form-form" class="form-validate">
      <?php echo $this->form->getInput('id') ?>
      <?php echo $this->form->getInput('created_by') ?>
      <table class="table">
        <thead>
          <tr><th colspan="2">Basic Settings</th></tr>
        </thead>
        <tbody>
          <!--tr>
            <td class="control-label"><?php echo $this->form->getLabel('state') ?></td>
            <td><?php echo $this->form->getInput('state') ?></td>
          </tr-->
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('title') ?></td>
      			<td><?php echo $this->form->getInput('title') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></td>
      			<td><?php echo $this->form->getValue('language') ? $this->form->getValue('language') : JText::_('JALL') ?></td>
          </tr>
        </tbody>
      </table>
      <?php echo $this->form->getInput('type') ?>
      <?php echo $this->form->getInput('theme') ?>
      <input type="hidden" id="jform_props" name="jform[props]" value="<?php echo htmlspecialchars($this->item->props, ENT_COMPAT, 'UTF-8') ?>" />
      <input type="hidden" id="jform_fields" name="jform[fields]" value="<?php echo htmlspecialchars($this->item->fields, ENT_COMPAT, 'UTF-8') ?>" />
      <?php echo $this->form->getInput('language') ?>
      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
    </form>
    <form name="layoutForm" id="layout-form">
      <table class="table">
        <thead>
          <tr><th colspan="2">Layout Settings</th></tr>
        </thead>
        <tbody>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_columns') ?></td>
      			<td><?php echo $this->form->getInput('layout_columns') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_margin') ?></td>
      			<td><?php echo $this->form->getInput('layout_margin') ?></td>
          </tr>
          <tr>
      			<td class="control-label"><?php echo $this->form->getLabel('layout_width') ?></td>
      			<td><?php echo $this->form->getInput('layout_width') ?></td>
          </tr>
        </tbody>
      </table>
    </form>
    <table class="table" id="advanced-form">
      <thead>
        <tr><th colspan="2">Advanced Settings</th></tr>
      </thead>
      <tbody>
        <tr>
          <td class="control-label"><label><?php echo JText::_('Hidden inputs') ?></label></td>
          <td class="ial-opts" id="ial-hidden">
            <div class="ial-opt">
              <input class="opt-option" type="text" placeholder="name" title="name"
              ><input class="opt-value" type="text" placeholder="value" title="value"
              ><label class="icon-plus" title="Add"></label
              ><label class="icon-trash" title="Delete"></label>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="tab-pane" id="elem-settings">
    <form name="elemForm" id="elem-form">
      <table class="table">
        <thead>
          <tr><th colspan="2">Basic Settings</th></tr>
        </thead>
        <tbody>
          <tr class="tr-wide">
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_wide') ?>
              <?php echo $this->form->getLabel('elem_wide') ?>
            </td>
          </tr>
          <tr class="tr-checked">
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_checked') ?>
              <?php echo $this->form->getLabel('elem_checked') ?>
            </td>
          </tr>
          <tr class="tr-required">
      			<td class="control-label"></td>
      			<td>
              <?php echo $this->form->getInput('elem_required') ?>
              <?php echo $this->form->getLabel('elem_required') ?>
            </td>
          </tr>
          <tr class="tr-label">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_label') ?></td>
      			<td><?php echo $this->form->getInput('elem_label') ?></td>
          </tr>
          <tr class="tr-subtitle">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_subtitle') ?></td>
      			<td><?php echo $this->form->getInput('elem_subtitle') ?></td>
          </tr>
          <!--tr>
      			<td class="control-label"><?php echo $this->form->getLabel('elem_icon') ?></td>
      			<td class="field-icon"><?php echo $this->form->getInput('elem_icon') ?></td>
          </tr-->
          <tr class="tr-placeholder">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_placeholder') ?></td>
      			<td><?php echo $this->form->getInput('elem_placeholder') ?></td>
          </tr>
          <tr class="tr-value">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_value') ?></td>
      			<td><?php echo $this->form->getInput('elem_value') ?></td>
          </tr>
          <tr class="tr-select">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_select') ?></td>
      			<td><?php echo $this->form->getInput('elem_select') ?></td>
          </tr>
          <tr class="tr-title">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_title') ?></td>
      			<td><?php echo $this->form->getInput('elem_title') ?></td>
          </tr>
          <tr class="tr-article">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_article') ?></td>
      			<td><?php echo $this->form->getInput('elem_article') ?></td>
          </tr>
          <tr class="tr-error">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_error') ?></td>
      			<td><?php echo $this->form->getInput('elem_error') ?></td>
          </tr>
          <tr class="tr-pattern">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_pattern') ?></td>
      			<td><?php echo $this->form->getInput('elem_pattern') ?></td>
          </tr>
        </tbody>
      </table>
      <table class="table" id="advanced-props">
        <thead>
          <tr><th colspan="2">Advanced Settings</th></tr>
        </thead>
        <tbody>
          <tr class="tr-class">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_class') ?></td>
      			<td><?php echo $this->form->getInput('elem_class') ?></td>
          </tr>
          <tr class="tr-name">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_name') ?></td>
      			<td><?php echo $this->form->getInput('elem_name') ?></td>
          </tr>
          <tr class="tr-prefix">
      			<td class="control-label"><?php echo $this->form->getLabel('elem_prefix') ?></td>
      			<td><?php echo $this->form->getInput('elem_prefix') ?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>