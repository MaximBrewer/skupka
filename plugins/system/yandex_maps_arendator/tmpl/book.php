<?php
defined('_JEXEC') or die;
?>
<?php
jimport( 'joomla.user.helper' ); 
$user = JFactory::getUser();
$profile = JUserHelper::getProfile($user->id);
?>
<div class="book_dialog">
    <div class="control-group">
        <label class="control-label" for="inputEmail"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_YOUR_EMAIL')?>:</label>
        <div class="controls">
            <input required value="<?php echo $user->email?>" type="mail" id="inputEmail" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_YOUR_EMAIL')?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputPhone"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_YOUR_PHONE')?>:</label>
        <div class="controls">
            <input required value="<?php echo $profile->profile['phone']?>" type="phone" id="inputPhone" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_YOUR_PHONE')?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="inputComment"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_COMMENT')?>:</label>
        <div class="controls">
            <textarea id="inputComment" placeholder="<?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_ENTER_YOUR_COMMENT')?>"></textarea>
        </div>
    </div>
    <input type="hidden" id="id"/>
</div>