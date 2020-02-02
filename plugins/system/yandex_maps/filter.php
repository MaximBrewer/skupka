<?php
/**
 * @var $map object
 */
defined('_JEXEC') or die;
$id = uniqid();
$show_toggle_all = $params->get('show_toggle_all', 0) ||  $map->settings->get('show_toggle_all', 0);
?>
<table>
    <?php if ($show_toggle_all) { ?>
    <tr>
        <td>
            <label>
                <input class="xdsoft_toggle_all" checked type="checkbox">
				<?=jtext::_('PLG_SYSTEM_YANDEX_MAPS_SHOW_TOGGLE_ALL')?>
            </label>
        </td>
    </tr>
    <?php } ?>
    <?php
        $cache = JFactory::getCache('com_yandex_maps');

        if ($cache) {
            $categories = $cache->call([$map, 'getCategoriesEx']);
        } else {
            $categories = $map->getCategoriesEx();
        }

        $active_categories = $map->settings->get('active_categories_in_filter', [0]);
        if (!$active_categories or !is_array($active_categories)) {
            $active_categories = array(0);
        }

        foreach ($active_categories as $i=>$val) {
            if ($val==0 || $val==-1) {
                $active_categories = array($val);
                break;
            }
        }
        
        
        $categories_in_filter = $map->settings->get('categories_in_filter', [0]);
        if (!$categories_in_filter or !is_array($categories_in_filter)) {
            $categories_in_filter = array(0);
        }

        foreach ($categories_in_filter as $i=>$val) {
            if ($val==0 || $val==-1) {
                $categories_in_filter = array($val);
                break;
            }
        }

        if ($params->get('filter_categories_view', 1)) {
            foreach($categories as $category) { 
                if (in_array(-1, $categories_in_filter)) {
                    break;
                }
                if (!in_array(0, $categories_in_filter) and  !in_array($category->id, $categories_in_filter)) {
                    continue;
                }
                ?>	
                <tr>
                    <td>
                    <label>
                        <input class="xdfilter_category" id="xdsoft_filter_by_category<?php echo $category->id?>" value="<?php echo $category->id?>" <?php echo (in_array($category->id, $active_categories) or in_array(0, $active_categories)) ? 'checked' : ''?>  type="checkbox"><?php echo $category->title?>
                    </label>
                    </td>
                </tr>
                <?php 
            }
        } else { ?>	
            <tr>
                <td>
                    <label for="filter_categories<?php echo $id?>"><?php echo JText::_('PLG_SYSTEM_YANDEX_MAPS_CATEGORY_SELECT')?></label>
                    <select data-placeholder="<?php echo JText::_('PLG_SYSTEM_YANDEX_MAPS_CATEGORY_SELECT')?>" multiple name="filter_categories" id="filter_categories<?php echo $id?>">
                        <?php foreach($categories as $category) { 
                            if (in_array(-1, $categories_in_filter)) {
                                break;
                            }
                            if (!in_array(0, $categories_in_filter) and  !in_array($category->id, $categories_in_filter)) {
                                continue;
                            }
                            ?>	
                                <option <?php echo (in_array($category->id, $active_categories) or in_array(0, $active_categories)) ? 'selected' : ''?> value="<?=$category->id?>"><?=$category->title?></option>
                            <?php 
                        } ?>
                    </select>
                </td>
            </tr>
        <?php } ?>
</table>
<script>
(function($){
	var timer, alltrigger = $('.xdsoft_filter input.xdsoft_toggle_all');
	alltrigger.on('change', function() {
        var checked = this.checked;
        $('.xdsoft_filter input.xdfilter_category').each(function(){
            this.checked = checked;
        }).eq(0).trigger('change');
    });
	$('.xdsoft_filter input.xdfilter_category').on('change', function() {
		clearTimeout(timer);
		timer = setTimeout(function () {
			var filter = [], allchecked = true;
			$('.xdsoft_filter input.xdfilter_category').each(function(){
				if (this.checked) {
					filter.push(/^[0-9]+$/.test($(this).val()) ? parseInt($(this).val(), 10) : $(this).val())
				} else {
                    allchecked = false;
                }
			});
			if (!allchecked && alltrigger.length && alltrigger[0].checked) {
                alltrigger[0].checked = false;
            }
			map<?php echo $map->id?>.setFilter(filter);
			var lst = [];
			for(var k in filter){
				if (filter.hasOwnProperty(k)) {
					lst.push('.xdsoft_category' + filter[k]);
				}
			}
			$('.xdsoft_item').not(lst.join(',')).hide();
			$(lst.join(',')).show();

		}, 300);
	});
    $('#filter_categories<?php echo $id?>').on('change', function () {
        map<?php echo $map->id?>.setFilter($(this).val());
    });
    if ($.fn.chosen) {
        $('#filter_categories<?php echo $id?>').chosen();
    }
}(window.XDjQuery || window.jQ || window.jQuery))
</script>