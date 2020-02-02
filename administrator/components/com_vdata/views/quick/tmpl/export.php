<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('formbehavior.chosen', 'select');
$mainframe = JFactory::getApplication();
JHTML::_('behavior.tooltip');

$context			= 'com_vdata.quick.list.';
$this->limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
$this->limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
    $db =  JFactory::getDbo();   
$this->limitstart = ($this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0);
$sql_qury = str_replace('#__', $db->getPrefix(), $mainframe->getUserStateFromRequest($context.'sql_query', 'sql_query', '', 'string')); 
?>

<script type="text/javascript">
jQuery(document).ready(function(){
	
jQuery('.exportoptions').hide();
jQuery("input[name='quick_or_custom']").on("change", function(){
	
if(jQuery(this).val()=='custom'){
jQuery('.exportoptions, .export_sub_options').show();

jQuery('#'+jQuery("select[name='what']").val()+'_options').show();
}
else {	
jQuery('.exportoptions, .format_specific_options, .export_sub_options').hide();	}
});
jQuery('.format_specific_options').hide();
jQuery('#'+jQuery("input[type=select][name='what']").val()+'_options').show();
});
jQuery(function(){

jQuery("input[type=radio][name='sql_structure_or_data']").on("change", function(){
	
if(jQuery(this).val()=='structure'){
jQuery('#sql_structure').show();
jQuery('#sql_data').hide();	}
else if(jQuery(this).val()=='data')
{
	jQuery('#sql_structure').hide();
    jQuery('#sql_data').show();	
}
else if(jQuery(this).val()=='structure_and_data'){
	jQuery('#sql_structure').show();
    jQuery('#sql_data').show();	
}	
	
});	
jQuery("select[name='what']").on("change", function(){
	jQuery('.format_specific_options').hide();
	jQuery('#'+jQuery(this).val()+'_options').show();

});	


})
</script>
<form action="index.php?option=com_vdata&view=quick" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="quick_or_custom">
    <h3><?php echo 'Export Method:'; ?></h3>
    <ul>
        <li>
            <?php echo '<input type="radio" name="quick_or_custom" value="quick" id="radio_quick_export" checked="checked" />';
            
            echo '<label class="export_label hasTip" title="'.JText::_('COM_VDATA_QUICK_EXPORT_QUICK_DISPLAY_DESC').'" for ="radio_quick_export">' .JText::_('COM_VDATA_QUICK_EXPORT_QUICK_DISPLAY') . '</label>'; ?>
        </li>
        <li>
            <?php echo '<input type="radio" name="quick_or_custom" value="custom" id="radio_custom_export"  />';
            
            echo '<label class="export_label hasTip" title="'.JText::_('COM_VDATA_QUICK_EXPORT_CUSTOM_DISPLAY_DESC').'" for="radio_custom_export">' .JText::_('COM_VDATA_QUICK_EXPORT_CUSTOM_DISPLAY'). '</label>';?>
        </li>
    </ul>
</div>
<div id="rows" class="exportoptions" style="display: block;">
        <?php if(empty($sql_qury)) {?>
        <h3><?php echo JText::_( 'COM_VDATA_NUMBER_OF_ROWS' ); ?></h3>
        <ul>
            <li>
                <input type="radio" id="radio_allrows_0" value="0" name="allrows"><label class="hasTip" title="<?php echo JText::_( 'COM_VDATA_DUMP_SOME_ROW_DESC' ); ?>" for="radio_allrows_0"><?php echo JText::_( 'COM_VDATA_DUMP_SOME_ROW' ); ?></label>
				<ul>
                    <li><label class="export_label hasTip" for="limit_to" title="<?php echo JText::_( 'COM_VDATA_NUMBER_OF_ROW_DESC' );?>"><?php echo JText::_( 'COM_VDATA_NUMBER_OF_ROW' ); ?></label> <input type="text" onfocus="this.select()" value="<?php echo $this->limit;?>" size="5" name="limit_to" id="limit_to"></li>
                    <li><label class="export_label hasTip" for="limit_from" title="<?php echo JText::_( 'COM_VDATA_ROW_BEGIN_FROM_DESC' ); ?>"><?php echo JText::_( 'COM_VDATA_ROW_BEGIN_FROM' ); ?></label> <input type="text" onfocus="this.select()" size="5" value="<?php echo $this->limitstart;?>" name="limit_from" id="limit_from"></li>
                </ul>
            </li>
            <li>
                <input type="radio" checked="checked" id="radio_allrows_1" value="1" name="allrows"> <label class="export_label hasTip" for="radio_allrows_1" title="<?php echo JText::_( 'COM_VDATA_DUMP_ALL_ROW_DESC' ); ?>"><?php echo JText::_( 'COM_VDATA_DUMP_ALL_ROW' ); ?></label>            </li>
        </ul>
		<?php } ?>
     </div>
	 <div id="output" class="exportoptions" style="display: block;">
    <h3><?php echo JText::_( 'COM_VDATA_OUTPUT' ); ?></h3> 
    <ul id="ul_output">
        <li>
            <input type="radio" checked="checked" id="radio_dump_asfile" value="sendit" name="output_format">
            <label class="export_label hasTip" title="<?php echo JText::_( 'COM_VDATA_SAVE_OUTPUT_TO_A_FILE_DESC' ); ?>" for="radio_dump_asfile"><?php echo JText::_( 'COM_VDATA_SAVE_OUTPUT_TO_A_FILE' ); ?></label>
            <ul id="ul_save_asfile">
                                <li style="opacity: 1;">
                    <label class="desc export_label hasTip" for="filename_template" title="<?php echo JText::_( 'COM_VDATA_FILE_NAME_TEMPLATE_DESC' ); ?>">
                    <?php echo JText::_( 'COM_VDATA_FILE_NAME_TEMPLATE' ); ?><sup class="footnotemarker" style="display: none;">1</sup>
                    <input type="text" value="@TABLE@" id="filename_template" name="filename_template">
                    
                </li>
             <li style="opacity: 1;"><label class="desc export_label hasTip" for="select_charset_of_file" title="<?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_DESC' ); ?>"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE' ); ?></label>
            <select size="1" name="charset_of_file" id="select_charset_of_file">
            <option value="iso-8859-1"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_1' ); ?></option>
			<option value="iso-8859-2"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_2' ); ?></option>
			<option value="iso-8859-3"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_3' ); ?></option>
			<option value="iso-8859-4"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_4' ); ?></option>
			<option value="iso-8859-5"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_5' ); ?></option>
			<option value="iso-8859-6"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_6' ); ?></option>
			<option value="iso-8859-7"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_7' ); ?></option>
			<option value="iso-8859-8"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_8' ); ?></option>
			<option value="iso-8859-9"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_9' ); ?></option>
			<option value="iso-8859-10"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_10' ); ?></option>
			<option value="iso-8859-11"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_11' ); ?></option>
			<option value="iso-8859-12"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_12' ); ?></option>
			<option value="iso-8859-13"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_13' ); ?></option>
			<option value="iso-8859-14"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_14' ); ?></option>
			<option value="iso-8859-15"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_ISO_8859_15' ); ?></option>
			<option value="windows-1250"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_WINDOW_1250' ); ?></option>
			<option value="windows-1251"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_WINDOW_1251' ); ?></option>
			<option value="windows-1252"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_WINDOW_1252' ); ?></option>
			<option value="windows-1256"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_WINDOW_1256' ); ?></option>
			<option value="windows-1257"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_WINDOW_1257' ); ?></option>
			<option value="koi8-r"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_KOI8' ); ?></option>
			<option value="big5"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_BIG_5' ); ?></option>
			<option value="gb2312"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_GB2312' ); ?></option>
			<option value="utf-16"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_UTF_16' ); ?></option>
			<option selected="selected" value="utf-8"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_UTF_8' ); ?></option>
			<option value="utf-7"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_UTF_7' ); ?></option>
			<option value="x-user-defined"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_X_USER_DEFINED' ); ?></option>
			<option value="euc-jp"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_EUC_JP' ); ?></option>
			<option value="ks_c_5601-1987"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_KS_C_5601' ); ?></option>
			<option value="tis-620"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_TIS_620' ); ?></option>
			<option value="SHIFT_JIS"><?php echo JText::_( 'COM_VDATA_CHARACTER_SET_OF_FILE_SHIFT_JIS' ); ?></option>
			</select></li>                 
			
                   
					<input type="hidden" value="none" name="compression">
                    
           
            </ul>
        </li>
        <li><input type="radio" value="astext" name="output_format" id="radio_view_as_text"><label class="export_label hasTip" for="radio_view_as_text" title="<?php echo JText::_( 'COM_VDATA_VIEW_OUTPUT_AS_TEXT_DESC' ); ?>"><?php echo JText::_( 'COM_VDATA_VIEW_OUTPUT_AS_TEXT' ); ?></label></li>
    </ul>
 
    </div>
    <div id="format">
	<h3><?php echo JText::_( 'COM_VDATA_EXPORT' ); ?></h3>
	
<select name="what" id="plugins">
			
			<option value="csv"><?php echo JText::_( 'COM_VDATA_EXPORT_CSV' ); ?></option>
			<option value="json"><?php echo JText::_( 'COM_VDATA_EXPORT_JSON' ); ?></option>
			<option value="php_array"><?php echo JText::_( 'COM_VDATA_EXPORT_PHP_ARRAY' ); ?></option>
			<?php $table_test = JFactory::getApplication()->input->get('table_names','');
			if($table_test!='') { ?> 
			<option value="sql" selected="selected"><?php echo JText::_( 'COM_VDATA_EXPORT_SQL' ); ?></option>
			<?php } ?>
			
			<option value="xml"><?php echo JText::_( 'COM_VDATA_EXPORT_XML' ); ?></option>
			<option value="yaml"><?php echo JText::_( 'COM_VDATA_EXPORT_YAML' ); ?></option>
			</select>

	</div>
  <div id="format_specific_opts" class="exportoptions" style="display: block;">
    <h3><?php echo JText::_('COM_VDATA_OPTIONS_FORMATES_SPECIFIC_OPTIONS');?></h3>
    <p id="scroll_to_options_msg" class="no_js_msg" style="display: none;"><?php echo JText::_('COM_VDATA_SCROLL_DOWN_TO_FILL_OPTIONS_FOR_SELECTED_FORMATE');?></p>
    <div class="format_specific_options" id="codegen_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="codegen_general_opts" class="export_sub_options">
<ul>

<li><input type="hidden" value="data" name="codegen_structure_or_data"></li>

<li>
<label class="desc export_label hasTip" for="select_codegen_format" title="<?php echo JText::_('COM_VDATA_OPTIONS_FORMATES_DESC');?>"><?php echo JText::_('COM_VDATA_OPTIONS_FORMATES');?></label><select id="select_codegen_format" name="codegen_format"><option selected="selected" value="0"><?php echo JText::_('COM_VDATA_OPTIONS_FORMATES_NHIBERNATE_C_DO');?></option><option value="1"><?php echo JText::_('COM_VDATA_OPTIONS_FORMATES_NHIBERNATE_C_XML');?></option></select></li>

</ul></div>
</div><div class="format_specific_options" id="csv_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="csv_general_opts" class="export_sub_options"><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_COLUMN_SEPARATED_WITH_DESC');?>" for="text_csv_separator"><?php echo JText::_('COM_VDATA_COLUMN_SEPARATED_WITH');?></label><input type="text" id="text_csv_separator" value="," name="csv_separator"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_COLUMN_ENCLOSED_WITH_DESC');?>" for="text_csv_enclosed"><?php echo JText::_('COM_VDATA_COLUMN_ENCLOSED_WITH');?></label><input type="text" id="text_csv_enclosed" value="&quot;" name="csv_enclosed"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_COLUMN_ESCAPED_WITH_DESC');?>" for="text_csv_escaped"><?php echo JText::_('COM_VDATA_COLUMN_ESCAPED_WITH');?></label><input type="text" id="text_csv_escaped" value="\" name="csv_escaped"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_LINE_TERMINATED_WITH_DESC');?>" for="text_csv_terminated"><?php echo JText::_('COM_VDATA_LINE_TERMINATED_WITH');?></label><input type="text" id="text_csv_terminated" value="AUTO" name="csv_terminated"></li>

<li>
<label class="desc export_label hasTip" for="text_csv_null" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_csv_null" value="NULL" name="csv_null"></li>

<li>
<input type="checkbox" id="checkbox_csv_removeCRLF" value="something" name="csv_removeCRLF"><label class="export_label hasTip" for="checkbox_csv_removeCRLF" title="<?php echo JText::_('COM_VDATA_REMOVE_CARRIAGE_RETURN_FEED_CARACTERS_DESC');?>"><?php echo JText::_('COM_VDATA_REMOVE_CARRIAGE_RETURN_FEED_CARACTERS');?></label></li>

<li>
<input type="checkbox" id="checkbox_csv_columns" value="something" name="csv_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_csv_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

<input type="hidden" value="data" name="csv_structure_or_data">

</ul></div>
</div><div class="format_specific_options" id="excel_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="excel_general_opts" class="export_sub_options"><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>" for="text_excel_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_excel_null" value="NULL" name="excel_null"></li>

<li>
<input type="checkbox" id="checkbox_excel_removeCRLF" value="something" name="excel_removeCRLF"><label class="hasTip" title="<?php echo JText::_('COM_VDATA_REMOVE_CARRIAGE_RETURN_FEED_CARACTERS_DESC');?>" for="checkbox_excel_removeCRLF"><?php echo JText::_('COM_VDATA_REMOVE_CARRIAGE_RETURN_FEED_CARACTERS');?></label></li>

<li>
<input type="checkbox" id="checkbox_excel_columns" value="something" name="excel_columns"><label class="hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>"  for="checkbox_excel_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_EXCEL_EDITIONS_DESC');?>" for="select_excel_edition"><?php echo JText::_('COM_VDATA_EXCEL_EDITIONS');?></label><select id="select_excel_edition" name="excel_edition"><option selected="selected" value="win"><?php echo JText::_('COM_VDATA_WINDOW_EDITIONS');?></option><option value="mac_excel2003"><?php echo JText::_('COM_VDATA_EXCEL_2003_MACINTOSH');?></option><option value="mac_excel2008"><?php echo JText::_('COM_VDATA_EXCEL_2008_MACINTOSH');?></option></select></li>

<li><input type="hidden" value="data" name="excel_structure_or_data"></li>

</ul></div>
</div><div class="format_specific_options" id="htmlword_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="htmlword_dump_what" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DUMP_TABLE');?></h3><ul>

<li><input type="radio" id="radio_htmlword_structure_or_data_structure" value="structure" name="htmlword_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_DESC');?>" for="radio_htmlword_structure_or_data_structure"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE');?></label></li><li><input type="radio" id="radio_htmlword_structure_or_data_data" value="data" name="htmlword_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA_DESC');?>" for="radio_htmlword_structure_or_data_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA');?>data</label></li><li><input type="radio" checked="checked" id="radio_htmlword_structure_or_data_structure_and_data" value="structure_and_data" name="htmlword_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA_DESC');?>" for="radio_htmlword_structure_or_data_structure_and_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA');?></label></li>

</ul></div>

<div id="htmlword_data" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DATA_DUMP_OPTIONS');?></h3><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>" for="text_htmlword_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_htmlword_null" value="NULL" name="htmlword_null"></li>

<li>
<input type="checkbox" id="checkbox_htmlword_columns" value="something" name="htmlword_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_htmlword_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

</ul></div>
</div><div class="format_specific_options" id="json_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="json_general_opts" class="export_sub_options"><ul>

<li><input type="hidden" value="data" name="json_structure_or_data"></li>

</ul></div>
<p><?php echo JText::_('COM_VDATA_THIS_FORMATE_HAS_NO_OPTIONS');?></p></div><div class="format_specific_options" id="latex_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="latex_general_opts" class="export_sub_options"><ul>

<li>
<input type="checkbox" checked="checked" id="checkbox_latex_caption" value="something" name="latex_caption"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_INCLUDE_TABLE_CAPTION_DESC');?>" for="checkbox_latex_caption"><?php echo JText::_('COM_VDATA_INCLUDE_TABLE_CAPTION');?></label></li>

</ul></div>

<div id="latex_dump_what" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DUMP_TABLE');?></h3><ul>

<li><input type="radio" id="radio_latex_structure_or_data_structure" value="structure" name="latex_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_DESC');?>" for="radio_latex_structure_or_data_structure"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE');?></label></li><li><input type="radio" id="radio_latex_structure_or_data_data" value="data" name="latex_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA_DESC');?>" for="radio_latex_structure_or_data_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA');?></label></li><li><input type="radio" checked="checked" id="radio_latex_structure_or_data_structure_and_data" value="structure_and_data" name="latex_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA_DESC');?>" for="radio_latex_structure_or_data_structure_and_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA');?></label></li>

</ul></div>

<div id="latex_structure" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_OBJECT_CREATION_OPTIONS');?></h3><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_DESC');?>" for="text_latex_structure_caption"><?php echo JText::_('COM_VDATA_TABLE_CAPTIONS');?></label><input type="text" id="text_latex_structure_caption" value="Structure of table @TABLE@" name="latex_structure_caption"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_CONTINUE_DESC');?>" for="text_latex_structure_continued_caption"><?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_CONTINUE');?></label><input type="text" id="text_latex_structure_continued_caption" value="Structure of table @TABLE@ (continued)" name="latex_structure_continued_caption"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_LABEL_KEY_DESC');?>" for="text_latex_structure_label"><?php echo JText::_('COM_VDATA_LABEL_KEY');?></label><input type="text" id="text_latex_structure_label" value="tab:@TABLE@-structure" name="latex_structure_label"></li>

<li>
<input type="checkbox" checked="checked" id="checkbox_latex_comments" value="something" name="latex_comments"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DISPLAY_COMMENTS_DESC');?>" for="checkbox_latex_comments"><?php echo JText::_('COM_VDATA_DISPLAY_COMMENTS');?></label></li>

</ul></div>

<div id="latex_data" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DATA_DUMP_OPTIONS');?></h3><ul>

<li>
<input type="checkbox" checked="checked" id="checkbox_latex_columns" value="something" name="latex_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_latex_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_DESC');?>" for="text_latex_data_caption"><?php echo JText::_('COM_VDATA_TABLE_CAPTIONS');?></label><input type="text" id="text_latex_data_caption" value="Content of table @TABLE@" name="latex_data_caption"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_CONTINUE_DESC');?>" for="text_latex_data_continued_caption"><?php echo JText::_('COM_VDATA_TABLE_CAPTIONS_CONTINUE');?></label><input type="text" id="text_latex_data_continued_caption" value="Content of table @TABLE@ (continued)" name="latex_data_continued_caption"></li>

<li>
<label class="desc hasTip" title="<?php echo JText::_('COM_VDATA_LABEL_KEY_DESC');?>" for="text_latex_data_label"><?php echo JText::_('COM_VDATA_LABEL_KEY');?></label><input type="text" id="text_latex_data_label" value="tab:@TABLE@-data" name="latex_data_label"></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>" for="text_latex_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_latex_null" value="\textit{NULL}" name="latex_null"></li>

</ul></div>
</div><div class="format_specific_options" id="mediawiki_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="mediawiki_general_opts" class="export_sub_options"><ul>

<li><input type="hidden" value="data" name="mediawiki_structure_or_data"></li>

</ul></div>
<p><?php echo JText::_('COM_VDATA_THIS_FORMATE_HAS_NO_OPTIONS');?></p></div><div class="format_specific_options" id="ods_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="ods_general_opts" class="export_sub_options"><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>" for="text_ods_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_ods_null" value="NULL" name="ods_null"></li>

<li>
<input type="checkbox" id="checkbox_ods_columns" value="something" name="ods_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_ods_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

<li><input type="hidden" value="data" name="ods_structure_or_data"></li>

</ul></div>
</div><div class="format_specific_options" id="odt_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="odt_general_opts" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DUMP_TABLE');?></h3><ul>

<li><input type="radio" id="radio_odt_structure_or_data_structure" value="structure" name="odt_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_DESC');?>" for="radio_odt_structure_or_data_structure"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE');?></label></li><li><input type="radio" id="radio_odt_structure_or_data_data" value="data" name="odt_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA_DESC');?>" for="radio_odt_structure_or_data_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA');?></label></li><li><input type="radio" checked="checked" id="radio_odt_structure_or_data_structure_and_data" value="structure_and_data" name="odt_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA_DESC');?>" for="radio_odt_structure_or_data_structure_and_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA');?></label></li>

</ul></div>

<div id="odt_structure" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_OBJECT_CREATION_OPTIONS');?></h3><ul>

<li>
<input type="checkbox" checked="checked" id="checkbox_odt_comments" value="something" name="odt_comments"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DISPLAY_COMMENTS_DESC');?>" for="checkbox_odt_comments"><?php echo JText::_('COM_VDATA_DISPLAY_COMMENTS');?></label></li>

</ul></div>

<div id="odt_data" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DATA_DUMP_OPTIONS');?></h3><ul>

<li>
<input type="checkbox" checked="checked" id="checkbox_odt_columns" value="something" name="odt_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_odt_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH_DESC');?>" for="text_odt_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_WITH');?></label><input type="text" id="text_odt_null" value="NULL" name="odt_null"></li>

</ul></div>
</div><div class="format_specific_options" id="pdf_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="pdf_general_opts" class="export_sub_options"><ul>

<li>
<p><?php echo JText::_('COM_VDATA_GENERATES_REPORT_CONTAINING_DATA_SINGLE_TABLE');?></p></li>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPORT_TITLE_DESC');?>" for="text_pdf_report_title"><?php echo JText::_('COM_VDATA_REPORT_TITLE');?></label><input type="text" id="text_pdf_report_title" value="" name="pdf_report_title"></li>

<li><input type="hidden" value="data" name="pdf_structure_or_data"></li>

</ul></div>
</div><div class="format_specific_options" id="php_array_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="php_array_general_opts" class="export_sub_options"><ul>

<li><input type="hidden" value="data" name="php_array_structure_or_data"></li>

</ul></div>
<p><?php echo JText::_('COM_VDATA_THIS_FORMATE_HAS_NO_OPTIONS');?></p></div><div class="format_specific_options" id="sql_options" style="display: block; border: 0px none; margin: 0px; padding: 0px;">
<div id="sql_general_opts" class="export_sub_options"><ul>


<li>
<input type="checkbox" checked="checked" id="checkbox_sql_include_comments" value="something" name="sql_include_comments"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPORT_TITLE_DESC');?>" for="checkbox_sql_include_comments"><?php echo JText::_('COM_VDATA_DISPLAY_COMMENTS_INCLUDES_INFO_EXPORT_TIMESTAMP');?></label></li>
<li class="subgroup"><ul id="ul_include_comments">

<li style="opacity: 1;">
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPORT_TITLE_DESC');?>" for="text_sql_header_comment"><?php echo JText::_('COM_VDATA_ADDITIONAL_CUSTOM_HEADER_COMMENTS');?></label><input type="text" id="text_sql_header_comment" value="" name="sql_header_comment"></li>

<li style="opacity: 1;">
<input type="checkbox" id="checkbox_sql_dates" value="something" name="sql_dates"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPORT_TITLE_DESC');?>" for="checkbox_sql_dates"><?php echo JText::_('COM_VDATA_INCLUDE_TIMESTAMP_DATABASE_CREATED_LAST_UPDATEED');?></label></li>

</ul></li>


 
<li class="subgroup"><ul>

<li><input type="radio" class="sql_structure_or_data" id="radio_sql_structure_or_data_structure" value="structure" name="sql_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_DESC');?>" for="radio_sql_structure_or_data_structure"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE');?></label></li><li><input type="radio" class="sql_structure_or_data" id="radio_sql_structure_or_data_data" value="data" name="sql_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA_DESC');?>" for="radio_sql_structure_or_data_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_DATA');?></label></li><li><input type="radio" class="sql_structure_or_data" checked="checked" id="radio_sql_structure_or_data_structure_and_data" value="structure_and_data" name="sql_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA_DESC');?>" for="radio_sql_structure_or_data_structure_and_data"><?php echo JText::_('COM_VDATA_DUMP_TABLE_STRUCTURE_AND_DATA');?></label></li>

</ul></li>

</ul></div>

<div id="sql_structure" class="export_sub_options" style="display: block;"><h3><?php echo JText::_('COM_VDATA_OBJECT_CREATION_OPTIONS');?></h3><ul>


<li>
<p><?php echo JText::_('COM_VDATA_ADDS_STATEMENTS_USE');?></p></li>
<li class="subgroup"><ul id="ul_add_statements">

<li>
<input type="checkbox" id="checkbox_sql_drop_table" value="something" name="sql_drop_table"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_ADD_DROP_TABLE_STATEMENTS_USE_DESC');?>" for="checkbox_sql_drop_table"><?php echo JText::_('COM_VDATA_ADD_DROP_TABLE_STATEMENTS_USE');?></label></li>

<li>
<input type="checkbox" checked="checked" id="checkbox_sql_create_table_statements" value="something" name="sql_create_table_statements"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_STATEMENTS_USE_DESC');?>" for="checkbox_sql_create_table_statements"><?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_STATEMENTS_USE');?></label></li>
<li class="subgroup"><ul id="ul_create_table_statements">

<li>
<input type="checkbox" checked="checked" id="checkbox_sql_if_not_exists" value="something" name="sql_if_not_exists"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_IF_NOT_EXISTS_DESC');?>" for="checkbox_sql_if_not_exists"><?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_IF_NOT_EXISTS');?></label></li>

<li>
<input type="checkbox" checked="checked" id="checkbox_sql_auto_increment" value="something" name="sql_auto_increment"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_AUTO_INCREMENT_DESC');?>" for="checkbox_sql_auto_increment"><?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_AUTO_INCREMENT');?></label></li>

</ul></li>

</ul></li>

<li>
<input type="checkbox" checked="checked" id="checkbox_sql_backquotes" value="something" name="sql_backquotes"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_CREATE_TABLE_OPTIONS_AUTO_INCREMENT_DESC');?>" for="checkbox_sql_backquotes"><?php echo JText::_('COM_VDATA_SYNTAX_ENCLOSE_TABLE_AND_FIELD_NAMES_WITHS_BACKQUOTES');?></label></li>

</ul></div>

<div id="sql_data" class="export_sub_options" style="display: none;"><h3><?php echo JText::_('COM_VDATA_DUMP_DATA_OPTIONS');?></h3><ul>


<li>
<p><?php echo JText::_('COM_VDATA_INSERT_STATEMENTS_USE');?></p></li>

<input type="hidden" id="checkbox_sql_ignore" value="INSERT" name="select_sql_type">

<p><?php echo JText::_('COM_VDATA_SYNTAX_TO_USE_WHEN_INSERTING_DATA');?></p></li>
<li class="subgroup"><ul>

<li><input type="radio" id="radio_sql_insert_syntax_complete" value="complete" name="sql_insert_syntax"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_COLUMN_NAME_IN_EVERY_DESC');?>" for="radio_sql_insert_syntax_complete"><?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_COLUMN_NAME_IN_EVERY');?></label></li><li><input type="radio" id="radio_sql_insert_syntax_extended" value="extended" name="sql_insert_syntax"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_MULTIPLE_ROW_IN_EVERY_DESC');?>" for="radio_sql_insert_syntax_extended"><?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_MULTIPLE_ROW_IN_EVERY');?></label></li><li><input type="radio" checked="checked" id="radio_sql_insert_syntax_both" value="both" name="sql_insert_syntax"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_BOTH_OF_ABOVE_DESC');?>" for="radio_sql_insert_syntax_both"><?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_BOTH_OF_ABOVE');?></label></li><li><input type="radio" id="radio_sql_insert_syntax_none" value="none" name="sql_insert_syntax"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_NEITHER_OF_ABOVE_DESC');?>" for="radio_sql_insert_syntax_none"><?php echo JText::_('COM_VDATA_SYNTAX_TO_INCLUDE_NEITHER_OF_ABOVE');?></label></li>

</ul></li>

</ul></div>
</div><div class="format_specific_options" id="texytext_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="texytext_general_opts" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DUMP_DATA_STRUCTURE_TABLE');?></h3><ul>

<li><input type="radio" id="radio_texytext_structure_or_data_structure" value="structure" name="texytext_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_STRUCTURE_DESC');?>" for="radio_texytext_structure_or_data_structure"><?php echo JText::_('COM_VDATA_DUMP_STRUCTURE');?></label></li><li><input type="radio" id="radio_texytext_structure_or_data_data" value="data" name="texytext_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_DATA_DESC');?>" for="radio_texytext_structure_or_data_data"><?php echo JText::_('COM_VDATA_DUMP_DATA');?></label></li><li><input type="radio" checked="checked" id="radio_texytext_structure_or_data_structure_and_data" value="structure_and_data" name="texytext_structure_or_data"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_DUMP_STRUCTURE_AND_DATA_DESC');?>" for="radio_texytext_structure_or_data_structure_and_data"><?php echo JText::_('COM_VDATA_DUMP_STRUCTURE_AND_DATA');?></label></li>

</ul></div>

<div id="texytext_data" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DUMP_DATA_OPTIONS');?></h3><ul>

<li>
<label class="desc export_label hasTip" title="<?php echo JText::_('COM_VDATA_REPLACE_NULL_BY_DESC');?>" for="text_texytext_null"><?php echo JText::_('COM_VDATA_REPLACE_NULL_BY');?></label><input type="text" id="text_texytext_null" value="NULL" name="texytext_null"></li>

<li>
<input type="checkbox" id="checkbox_texytext_columns" value="something" name="texytext_columns"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW_DESC');?>" for="checkbox_texytext_columns"><?php echo JText::_('COM_VDATA_PUT_COLUMN_NAME_IN_FIRST_ROW');?></label></li>

</ul></div>
</div><div class="format_specific_options" id="xml_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="xml_general_opts" class="export_sub_options"><ul>

<li><input type="hidden" value="data" name="xml_structure_or_data"></li>

</ul></div>


<div id="xml_data" class="export_sub_options"><h3><?php echo JText::_('COM_VDATA_DATA_DUMP_OPTIONS');?></h3><ul>
<?php if(empty($sql_qury)) {?>
<li>
<input type="checkbox" checked="checked" id="checkbox_xml_export_tables" value="something" name="xml_export_tables"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_EXPORT_DATA_TABLE_DESC');?>" for="checkbox_xml_export_tables"><?php echo JText::_('COM_VDATA_EXPORT_DATA_TABLE');?></label></li>
<?php } ?>
<li>
<input type="checkbox" checked="checked" id="checkbox_xml_export_contents" value="something" name="xml_export_contents"><label class="export_label hasTip" title="<?php echo JText::_('COM_VDATA_EXPORT_DATA_CONTENTS_DESC');?>" for="checkbox_xml_export_contents"><?php echo JText::_('COM_VDATA_EXPORT_DATA_CONTENTS');?></label></li>

</ul></div>
</div><div class="format_specific_options" id="yaml_options" style="display: none; border: 0px none; margin: 0px; padding: 0px;">
<div id="yaml_general_opts" class="export_sub_options"><ul>

<li><input type="hidden" value="data" name="yaml_structure_or_data"></li>

</ul></div>
</div></div>

<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); 

?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="quick" />
<input type="hidden" name="task" value="export_data" />
<input type="hidden" name="limit" value="" />
<input type="hidden" name="format" value="" />
<input type="hidden" name="table_name" value="<?php echo JFactory::getApplication()->input->get('table_names','');?>" />
<input type="hidden" name="sql_query" value="<?php echo $sql_qury;?>" />
<input type="hidden" name="checked_id" value="<?php echo JFactory::getApplication()->input->get('checked_id','');?>" />
<input type="hidden" name="checked_value" value="<?php echo JFactory::getApplication()->input->get('checked_value','');?>" />
<input type="submit" id="buttonGo" class="btn" value="Go">
</form>
