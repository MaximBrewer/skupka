<?php
defined("_JEXEC") or die("Access deny");

foreach ($this->items as $item) { ?>
    <option
		data-lat="<?=$item->lat?>" 
		data-lan="<?=$item->lan?>" 
		data-zoom="<?=$item->zoom?>" 
		value="<?=$item->id?>"
	><?=$item->title?></option>
    <?php
}