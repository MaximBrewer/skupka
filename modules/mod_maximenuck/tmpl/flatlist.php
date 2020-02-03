<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
$tmpitem = reset($items);
$columnstylesbegin = isset($tmpitem->columnwidth) ? ' style="width:' . modMaximenuckHelper::testUnit($tmpitem->columnwidth) . ';float:left;"' : '';
$orientation_class = ( $params->get('orientation', 'horizontal') == 'vertical' ) ? 'maximenuckv' : 'maximenuckh';
$start = (int) $params->get('startLevel');
$direction = $langdirection == 'rtl' ? 'right' : 'left';
?>
<!-- debut maximenu CK, par cedric keiflin -->
<div class="<?php echo $orientation_class . ' ' . $langdirection ?>" id="<?php echo $params->get('menuid', 'maximenuck'); ?>" >
        <div class="maximenuck2"<?php echo $columnstylesbegin; ?>>
            <ul class="maximenuck2 <?php echo $params->get('moduleclass_sfx'); ?>">
<?php
$zindex = 12000;
$lastitem = '';
foreach ($items as $i => &$item) {
	$item->mobile_data = isset($item->mobile_data) ? $item->mobile_data : '';
	$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
	if ($params->get('calledfromlevel')) {
		$itemlevel = $itemlevel + $params->get('calledfromlevel') - 1;
	}
	$createnewrow = (isset($item->createnewrow) AND $item->createnewrow) ? '<div style="clear:both;"></div>' : '';
	$columnstyles = isset($item->columnwidth) ? ' style="width:' . modMaximenuckHelper::testUnit($item->columnwidth) . ';float:left;"' : '';
	 if (isset($item->colonne) AND (isset($items[$lastitem]) AND !$items[$lastitem]->deeper)) {
        echo '</ul><div class="clr"></div></div>'.$createnewrow.'<div class="maximenuck2" ' . $columnstyles . '><ul class="maximenuck2">';
     }
    if (isset($item->content) AND $item->content) {
        echo '<li class="maximenuck maximenuflatlistck '. $item->classe . ' level' . $itemlevel .' '.$item->liclass . '" data-level="' . $itemlevel . '" ' . $item->mobile_data . '>' . $item->content;
		$item->ftitle = '';
    }


    if ($item->ftitle != "") {
		$title = $item->anchor_title ? ' title="'.$item->anchor_title.'"' : '';
		$description = $item->desc ? '<span class="descck">' . $item->desc . '</span>' : '';
		// manage HTML encapsulation
		// $item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
		$classcoltitle = $item->params->get('maximenu_classcoltitle', '') ? ' class="'.$item->params->get('maximenu_classcoltitle', '').'"' : '';
		// if ($item->tagcoltitle != 'none') {
			// $item->ftitle = '<'.$item->tagcoltitle.$classcoltitle.'>'.$item->ftitle.'</'.$item->tagcoltitle.'>';
		// }
		$opentag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '<'.$item->tagcoltitle.$classcoltitle.'>' : '';
		$closetag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '</'.$item->tagcoltitle.'>' : '';

		// manage image
		require dirname(__FILE__) . '/_image.php';

		if ($params->get('imageonly', '0') == '1')
			$item->ftitle = '';
		echo '<li class="maximenuck maximenuflatlistck '. $item->classe . ' level' . $itemlevel .' '.$item->liclass . '" style="z-index : ' . $zindex . ';" data-level="' . $itemlevel . '" ' . $item->mobile_data . '>';
		require dirname(__FILE__) . '/_itemtype.php';
	}

    /*if ($item->deeper) {

        if (isset($item->submenuswidth) || $item->leftmargin || $item->topmargin || $item->colbgcolor) {
            $item->styles = "style=\"";
            if ($item->leftmargin)
                $item->styles .= "margin-left:" . $item->leftmargin . "px;";
            if ($item->topmargin)
                $item->styles .= "margin-top:" . $item->topmargin . "px;";
            if (isset($item->submenuswidth))
                $item->styles .= "width:" . $item->submenuswidth . "px;";
            if (isset($item->colbgcolor) && $item->colbgcolor)
                $item->styles .= "background:" . $item->colbgcolor . ";";

            $item->styles .= "\"";
        } else {
            $item->styles = "";
        }

        echo "\n\t<div class=\"floatck\" " . $item->styles . ">" . $close . "<div class=\"maxidrop-top\"><div class=\"maxidrop-top2\"></div></div><div class=\"maxidrop-main\"><div class=\"maxidrop-main2\"><div class=\"maximenuCK2 first \" " . $columnstyles . ">\n\t<ul class=\"maximenuCK2\">";
        if (isset($item->coltitle))
            echo $item->coltitle;
    }*/
    // The next item is shallower.
    /*elseif ($item->shallower) {
        echo "\n\t</li>";
        echo str_repeat("\n\t</ul>\n\t<div class=\"clr\"></div></div><div class=\"clr\"></div></div></div><div class=\"maxidrop-bottom\"><div class=\"maxidrop-bottom2\"></div></div></div>\n\t</li>", $item->level_diff);
    }*/
    // the item is the last.
    /*elseif ($item->is_end) {
        echo str_repeat("</li>\n\t</ul>\n\t<div class=\"clr\"></div></div><div class=\"clr\"></div></div></div><div class=\"maxidrop-bottom\"><div class=\"maxidrop-bottom2\"></div></div></div>", $item->level_diff);
        echo "</li>";
    }*/
    // The next item is on the same level.
    // else {
        // if (!isset($item->colonne))
            echo "\n\t\t</li>\n";
    // }

    $zindex--;
    $lastitem = $i;
}
?>
            </ul>
			<div style="clear:both;"></div>
        </div>
	</div>
    <!-- fin maximenuCK -->
