<?php
defined('JPATH_PLATFORM') or die;
JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');

JHtml::_('jquery.framework');
JHtml::_('xdwork.dialog');
jhtml::_('xdwork.datetimepicker');

Jhtml::_('xdwork.includejs', JURI::root().'plugins/system/yandex_maps_arendator/assets/jodit/jquery.jodit.min.js');
Jhtml::_('xdwork.includejs', JURI::root().'plugins/system/yandex_maps_arendator/assets/profile.js');

Jhtml::_('xdwork.includecss', JURI::root().'plugins/system/yandex_maps_arendator/assets/jodit/jquery.jodit.min.css');
Jhtml::_('xdwork.includecss', JURI::root().'plugins/system/yandex_maps_arendator/assets/style.css');

Jhtml::_('xdwork.includecss',JURI::root().'plugins/system/yandex_maps_arendator/assets/chosen/chosen.css');
Jhtml::_('xdwork.includejs',JURI::root().'plugins/system/yandex_maps_arendator/assets/chosen/chosen.jquery.js');

include_once JPATH_ROOT.'/administrator/components/com_yandex_maps/helpers/CModel.php';
JModelLegacy::addIncludePath(JPATH_ROOT.'/administrator/components/com_yandex_maps/models/');
$categories = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model(1)->categories;
?>

<div style="margin:10px 0;">
    <button type="button" onclick="addObject()" class="btn btn-primary"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ADD_OBJECT');?></button>
</div>

<table id="objects" class="table table-stripped table-hover table-bordered">
    <thead>
        <tr>
            <th><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OBJECT_NAME');?></th>
            <th><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OPERATION');?></th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>

<div id="objectappenddialog">
    <fieldset>
        <div class="control-group">
            <label><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OBJECT_NAME');?></label>
            <input class="like" id="object_title" name="object[title]" type="text" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_OBJECT_NAME');?>">
        </div>
        <div class="control-group">
            <label><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OBJECT_DESCRIPTION');?></label>
            <textarea id="object_description" name="object[description]" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_OBJECT_DESCRIPTION');?>" type="text"></textarea>
         </div>
        <div class="control-group">
            <div id="uploaderbox"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_DRAG_AND_DROP_FILES_HERE');?><input accept="image/*" tabindex="-1" dir="auto" multiple="" type="file"/></div>
            <div id="images" class="images"></div>
        </div>
        <div class="control-group">
            <label><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OBJECT_PRICE');?></label>
            <input class="like" id="object_price" name="object[price]" type="text" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_OBJECT_PRICE');?>">
            <span class="help-block"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_OBJECT_PRICE_HINT');?></span>
        </div>
        <div class="control-group">
            <label><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OBJECT_HOURS');?></label>
            <div id="timer" class="timer">
                <a class="timer_add_day" href="javascript:void(0)"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ADD_DAY');?></a>
                <div class="timer_popap">
                    <div class="input-append">
                        <input class="span2 datepicker" type="text">
                        <button type="button" class="btn addday"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ADD');?></button>
                    </div>
                </div>
                <table class="table_days table table-stripped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_DAY');?></th>
                            <th><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_HOURS');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="control-group">
            <label><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_CATEGORY');?></label>
            <select name="categories" multiple  data-placeholder="<?php echo JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_SELECT_CATEGORIES')?>" id="categories">
                <?php 
                    foreach ($categories as $category) { ?>
                        <option value="<?php echo $category->id?>"><?php echo $category->title?></option>
                    <?php }
                ?>
            </select>
        </div>
        <div class="control-group">
            <?php echo jhtml::_('xdwork.address', 'location', 'null', array('autocomplete'=>0, 'autoinit'=> 0, 'width' => 500));?>
        </div>
        <input type="hidden" id="object_id" name="object[id]"/>
    </fieldset>
</div>