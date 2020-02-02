<?php
defined('_JEXEC') or die;
$params = json_decode($object->params ?: '{}');
if (jFactory::getUser()->get('isRoot')) { ?>
    <div class="info">
        <table class="table_days_render table table-stripped table-hover table-bordered table-condensed">
            <tr>
                <th>Информация для админа:</th>
                <td>Объект - <?php echo $object->active ? 'Опубликован' : 'Не опубликован'?></td>
                <td>
                    <a href="<?php echo   JURI::root().'plugins/system/yandex_maps_arendator/connector.php?action='.(!$object->active ? 'public' : 'unpublic').'&id='.$object->id?>"><?php echo !$object->active ? 'Опубликовать' : 'Снять с публикации'?></a>
                    <br><a href="<?php echo   JURI::root().'plugins/system/yandex_maps_arendator/connector.php?action=delete&id='.$object->id?>">Удалить</a>
                </td>
            </tr>
        </table>
    </div>
<?php }
if ($params->price) { ?>
<h4>Цена: <?php echo $params->price;?> <?php echo JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_CURRENCY')?></h4>
<?php }
if (!$ballon and $params->images && count($params->images)) { ?>
    <div id="owl-example" class="owl-carousel">
    <?php foreach ($params->images as $image) { ?>
        <div><img src="<?php echo JURI::root().jhtml::_('xdwork.thumb','images/'.$image, 300, 300, 1);?>" alt=""></div>
    <?php } ?>
    </div>
<?php }

$db = jFactory::getDBO();
$datetimes = $db->setQuery('select * from #__yandex_maps_datetimes where object_id='.((int)$object->id).' order by date_value, time_value')->loadObjectList();
if (count($datetimes)) { ?>
    <table class="table_days_render table table-stripped table-hover table-bordered table-condensed">
        <tbody>
        <?php
        $dt = '';
        setlocale(LC_ALL, jFactory::getlanguage()->getTag().'.UTF-8');
        foreach ($datetimes as $times) {
            if ($dt !== $times->date_value) {
                $dt = $times->date_value;
                ?>
                <tr>
                    <th colspan="4" class="day"><?php echo JHTML::_('date', strtotime($times->date_value), 'l, j F')?></th>
                </tr>
                <?php
            } ?>
            <tr>
                <td class="time"><?php echo date('H:i', strtotime($times->time_value))?></td>
                <td class="time_status time_price time_status<?php echo $times->status?>"><?php echo !$times->status ? ($times->price ?: $params->price).' '.JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_CURRENCY') : '-'?></td>
                <td class="time_status time_status<?php echo $times->status?>"><?php echo jtext::_($statuses[$times->status])?></td>
                <td class="time_manage">
                    <?php if (!jFactory::getUser()->id) { 
                        $joomlaLoginUrl = 'index.php?option=com_users&view=login';
                        $finalUrl = JRoute::_($joomlaLoginUrl . '&return='.urlencode(base64_encode(jURI::current())));
                    ?>
                        <a href="<?php echo $finalUrl?>"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_LOGIN')?></a>
                    <?php } elseif (!$times->status) { ?>
                        <a onclick="bookDialog(<?php echo $times->id?>)" href="javascript:void(0)"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_BOOK')?></a>
                    <?php } elseif ($times->book_user && $times->book_user === jFactory::getUser()->id) { ?>
                        <a onclick="deleteBook(<?php echo $times->id?>)" href="javascript:void(0)"><?php echo jtext::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_DELETE_BOOK')?></a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>