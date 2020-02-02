<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
/**
 * Cache Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since		1.6
 */
class JModelEdittheme extends OPCModel
{
  function getPreview()
  {
  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
	  	  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		  $msg = ''; 
		  if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview'))
		  if (JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview')===false)
		  {
			$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview').'<br />'; 
		  }
		  if (JFolder::copy(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template, JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview', '', true )===false)
		  {
			$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', '', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview').'<br />'; 
		  }
		  return $msg; 
	  
  }
  
  function getCss()
   {
     	  $suffix = '_preview'; 
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		  
		 
		  
		  $files = JFolder::files(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix, 'css', 1, true, array('.svn', 'CVS')); 
		   
		  return $files; 
		

   }
#http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return $color;
}
//http://stackoverflow.com/questions/2993970/function-that-converts-hex-color-values-to-an-approximate-color-name
   function getColorCode($colorName)
   {
	   $colors = array(
"cloudy blue" => array(172, 194, 217),
"dark pastel green" => array(86, 174, 87),
"dust" => array(178, 153, 110),
"electric lime" => array(168, 255, 4),
"fresh green" => array(105, 216, 79),
"light eggplant" => array(137, 69, 133),
"nasty green" => array(112, 178, 63),
"really light blue" => array(212, 255, 255),
"tea" => array(101, 171, 124),
"warm purple" => array(149, 46, 143),
"yellowish tan" => array(252, 252, 129),
"cement" => array(165, 163, 145),
"dark grass green" => array(56, 128, 4),
"dusty teal" => array(76, 144, 133),
"grey teal" => array(94, 155, 138),
"macaroni and cheese" => array(239, 180, 53),
"pinkish tan" => array(217, 155, 130),
"spruce" => array(10, 95, 56),
"strong blue" => array(12, 6, 247),
"toxic green" => array(97, 222, 42),
"windows blue" => array(55, 120, 191),
"blue blue" => array(34, 66, 199),
"blue with a hint of purple" => array(83, 60, 198),
"booger" => array(155, 181, 60),
"bright sea green" => array(5, 255, 166),
"dark green blue" => array(31, 99, 87),
"deep turquoise" => array(1, 115, 116),
"green teal" => array(12, 181, 119),
"strong pink" => array(255, 7, 137),
"bland" => array(175, 168, 139),
"deep aqua" => array(8, 120, 127),
"lavender pink" => array(221, 133, 215),
"light moss green" => array(166, 200, 117),
"light seafoam green" => array(167, 255, 181),
"olive yellow" => array(194, 183, 9),
"pig pink" => array(231, 142, 165),
"deep lilac" => array(150, 110, 189),
"desert" => array(204, 173, 96),
"dusty lavender" => array(172, 134, 168),
"purpley grey" => array(148, 126, 148),
"purply" => array(152, 63, 178),
"candy pink" => array(255, 99, 233),
"light pastel green" => array(178, 251, 165),
"boring green" => array(99, 179, 101),
"kiwi green" => array(142, 229, 63),
"light grey green" => array(183, 225, 161),
"orange pink" => array(255, 111, 82),
"tea green" => array(189, 248, 163),
"very light brown" => array(211, 182, 131),
"egg shell" => array(255, 252, 196),
"eggplant purple" => array(67, 5, 65),
"powder pink" => array(255, 178, 208),
"reddish grey" => array(153, 117, 112),
"baby shit brown" => array(173, 144, 13),
"liliac" => array(196, 142, 253),
"stormy blue" => array(80, 123, 156),
"ugly brown" => array(125, 113, 3),
"custard" => array(255, 253, 120),
"darkish pink" => array(218, 70, 125),
"deep brown" => array(65, 2, 0),
"greenish beige" => array(201, 209, 121),
"manilla" => array(255, 250, 134),
"off blue" => array(86, 132, 174),
"battleship grey" => array(107, 124, 133),
"browny green" => array(111, 108, 10),
"bruise" => array(126, 64, 113),
"kelley green" => array(0, 147, 55),
"sickly yellow" => array(208, 228, 41),
"sunny yellow" => array(255, 249, 23),
"azul" => array(29, 93, 236),
"darkgreen" => array(5, 73, 7),
"green/yellow" => array(181, 206, 8),
"lichen" => array(143, 182, 123),
"light light green" => array(200, 255, 176),
"pale gold" => array(253, 222, 108),
"sun yellow" => array(255, 223, 34),
"tan green" => array(169, 190, 112),
"burple" => array(104, 50, 227),
"butterscotch" => array(253, 177, 71),
"toupe" => array(199, 172, 125),
"dark cream" => array(255, 243, 154),
"indian red" => array(133, 14, 4),
"light lavendar" => array(239, 192, 254),
"poison green" => array(64, 253, 20),
"baby puke green" => array(182, 196, 6),
"bright yellow green" => array(157, 255, 0),
"charcoal grey" => array(60, 65, 66),
"squash" => array(242, 171, 21),
"cinnamon" => array(172, 79, 6),
"light pea green" => array(196, 254, 130),
"radioactive green" => array(44, 250, 31),
"raw sienna" => array(154, 98, 0),
"baby purple" => array(202, 155, 247),
"cocoa" => array(135, 95, 66),
"light royal blue" => array(58, 46, 254),
"orangeish" => array(253, 141, 73),
"rust brown" => array(139, 49, 3),
"sand brown" => array(203, 165, 96),
"swamp" => array(105, 131, 57),
"tealish green" => array(12, 220, 115),
"burnt siena" => array(183, 82, 3),
"camo" => array(127, 143, 78),
"dusk blue" => array(38, 83, 141),
"fern" => array(99, 169, 80),
"old rose" => array(200, 127, 137),
"pale light green" => array(177, 252, 153),
"peachy pink" => array(255, 154, 138),
"rosy pink" => array(246, 104, 142),
"light bluish green" => array(118, 253, 168),
"light bright green" => array(83, 254, 92),
"light neon green" => array(78, 253, 84),
"light seafoam" => array(160, 254, 191),
"tiffany blue" => array(123, 242, 218),
"washed out green" => array(188, 245, 166),
"browny orange" => array(202, 107, 2),
"nice blue" => array(16, 122, 176),
"sapphire" => array(33, 56, 171),
"greyish teal" => array(113, 159, 145),
"orangey yellow" => array(253, 185, 21),
"parchment" => array(254, 252, 175),
"straw" => array(252, 246, 121),
"very dark brown" => array(29, 2, 0),
"terracota" => array(203, 104, 67),
"ugly blue" => array(49, 102, 138),
"clear blue" => array(36, 122, 253),
"creme" => array(255, 255, 182),
"foam green" => array(144, 253, 169),
"grey/green" => array(134, 161, 125),
"light gold" => array(253, 220, 92),
"seafoam blue" => array(120, 209, 182),
"topaz" => array(19, 187, 175),
"violet pink" => array(251, 95, 252),
"wintergreen" => array(32, 249, 134),
"yellow tan" => array(255, 227, 110),
"dark fuchsia" => array(157, 7, 89),
"indigo blue" => array(58, 24, 177),
"light yellowish green" => array(194, 255, 137),
"pale magenta" => array(215, 103, 173),
"rich purple" => array(114, 0, 88),
"sunflower yellow" => array(255, 218, 3),
"green/blue" => array(1, 192, 141),
"leather" => array(172, 116, 52),
"racing green" => array(1, 70, 0),
"vivid purple" => array(153, 0, 250),
"dark royal blue" => array(2, 6, 111),
"hazel" => array(142, 118, 24),
"muted pink" => array(209, 118, 143),
"booger green" => array(150, 180, 3),
"canary" => array(253, 255, 99),
"cool grey" => array(149, 163, 166),
"dark taupe" => array(127, 104, 78),
"darkish purple" => array(117, 25, 115),
"true green" => array(8, 148, 4),
"coral pink" => array(255, 97, 99),
"dark sage" => array(89, 133, 86),
"dark slate blue" => array(33, 71, 97),
"flat blue" => array(60, 115, 168),
"mushroom" => array(186, 158, 136),
"rich blue" => array(2, 27, 249),
"dirty purple" => array(115, 74, 101),
"greenblue" => array(35, 196, 139),
"icky green" => array(143, 174, 34),
"light khaki" => array(230, 242, 162),
"warm blue" => array(75, 87, 219),
"dark hot pink" => array(217, 1, 102),
"deep sea blue" => array(1, 84, 130),
"carmine" => array(157, 2, 22),
"dark yellow green" => array(114, 143, 2),
"pale peach" => array(255, 229, 173),
"plum purple" => array(78, 5, 80),
"golden rod" => array(249, 188, 8),
"neon red" => array(255, 7, 58),
"old pink" => array(199, 121, 134),
"very pale blue" => array(214, 255, 254),
"blood orange" => array(254, 75, 3),
"grapefruit" => array(253, 89, 86),
"sand yellow" => array(252, 225, 102),
"clay brown" => array(178, 113, 61),
"dark blue grey" => array(31, 59, 77),
"flat green" => array(105, 157, 76),
"light green blue" => array(86, 252, 162),
"warm pink" => array(251, 85, 129),
"dodger blue" => array(62, 130, 252),
"gross green" => array(160, 191, 22),
"ice" => array(214, 255, 250),
"metallic blue" => array(79, 115, 142),
"pale salmon" => array(255, 177, 154),
"sap green" => array(92, 139, 21),
"algae" => array(84, 172, 104),
"bluey grey" => array(137, 160, 176),
"greeny grey" => array(126, 160, 122),
"highlighter green" => array(27, 252, 6),
"light light blue" => array(202, 255, 251),
"light mint" => array(182, 255, 187),
"raw umber" => array(167, 94, 9),
"vivid blue" => array(21, 46, 255),
"deep lavender" => array(141, 94, 183),
"dull teal" => array(95, 158, 143),
"light greenish blue" => array(99, 247, 180),
"mud green" => array(96, 102, 2),
"pinky" => array(252, 134, 170),
"red wine" => array(140, 0, 52),
"shit green" => array(117, 128, 0),
"tan brown" => array(171, 126, 76),
"darkblue" => array(3, 7, 100),
"rosa" => array(254, 134, 164),
"lipstick" => array(213, 23, 78),
"pale mauve" => array(254, 208, 252),
"claret" => array(104, 0, 24),
"dandelion" => array(254, 223, 8),
"orangered" => array(254, 66, 15),
"poop green" => array(111, 124, 0),
"ruby" => array(202, 1, 71),
"dark" => array(27, 36, 49),
"greenish turquoise" => array(0, 251, 176),
"pastel red" => array(219, 88, 86),
"piss yellow" => array(221, 214, 24),
"bright cyan" => array(65, 253, 254),
"dark coral" => array(207, 82, 78),
"algae green" => array(33, 195, 111),
"darkish red" => array(169, 3, 8),
"reddy brown" => array(110, 16, 5),
"blush pink" => array(254, 130, 140),
"camouflage green" => array(75, 97, 19),
"lawn green" => array(77, 164, 9),
"putty" => array(190, 174, 138),
"vibrant blue" => array(3, 57, 248),
"dark sand" => array(168, 143, 89),
"purple/blue" => array(93, 33, 208),
"saffron" => array(254, 178, 9),
"twilight" => array(78, 81, 139),
"warm brown" => array(150, 78, 2),
"bluegrey" => array(133, 163, 178),
"bubble gum pink" => array(255, 105, 175),
"duck egg blue" => array(195, 251, 244),
"greenish cyan" => array(42, 254, 183),
"petrol" => array(0, 95, 106),
"royal" => array(12, 23, 147),
"butter" => array(255, 255, 129),
"dusty orange" => array(240, 131, 58),
"off yellow" => array(241, 243, 63),
"pale olive green" => array(177, 210, 123),
"orangish" => array(252, 130, 74),
"leaf" => array(113, 170, 52),
"light blue grey" => array(183, 201, 226),
"dried blood" => array(75, 1, 1),
"lightish purple" => array(165, 82, 230),
"rusty red" => array(175, 47, 13),
"lavender blue" => array(139, 136, 248),
"light grass green" => array(154, 247, 100),
"light mint green" => array(166, 251, 178),
"sunflower" => array(255, 197, 18),
"velvet" => array(117, 8, 81),
"brick orange" => array(193, 74, 9),
"lightish red" => array(254, 47, 74),
"pure blue" => array(2, 3, 226),
"twilight blue" => array(10, 67, 122),
"violet red" => array(165, 0, 85),
"yellowy brown" => array(174, 139, 12),
"carnation" => array(253, 121, 143),
"muddy yellow" => array(191, 172, 5),
"dark seafoam green" => array(62, 175, 118),
"deep rose" => array(199, 71, 103),
"dusty red" => array(185, 72, 78),
"grey/blue" => array(100, 125, 142),
"lemon lime" => array(191, 254, 40),
"purple/pink" => array(215, 37, 222),
"brown yellow" => array(178, 151, 5),
"purple brown" => array(103, 58, 63),
"wisteria" => array(168, 125, 194),
"banana yellow" => array(250, 254, 75),
"lipstick red" => array(192, 2, 47),
"water blue" => array(14, 135, 204),
"brown grey" => array(141, 132, 104),
"vibrant purple" => array(173, 3, 222),
"baby green" => array(140, 255, 158),
"barf green" => array(148, 172, 2),
"eggshell blue" => array(196, 255, 247),
"sandy yellow" => array(253, 238, 115),
"cool green" => array(51, 184, 100),
"pale" => array(255, 249, 208),
"blue/grey" => array(117, 141, 163),
"hot magenta" => array(245, 4, 201),
"greyblue" => array(119, 161, 181),
"purpley" => array(135, 86, 228),
"baby shit green" => array(136, 151, 23),
"brownish pink" => array(194, 126, 121),
"dark aquamarine" => array(1, 115, 113),
"diarrhea" => array(159, 131, 3),
"light mustard" => array(247, 213, 96),
"pale sky blue" => array(189, 246, 254),
"turtle green" => array(117, 184, 79),
"bright olive" => array(156, 187, 4),
"dark grey blue" => array(41, 70, 91),
"greeny brown" => array(105, 96, 6),
"lemon green" => array(173, 248, 2),
"light periwinkle" => array(193, 198, 252),
"seaweed green" => array(53, 173, 107),
"sunshine yellow" => array(255, 253, 55),
"ugly purple" => array(164, 66, 160),
"medium pink" => array(243, 97, 150),
"puke brown" => array(148, 119, 6),
"very light pink" => array(255, 244, 242),
"viridian" => array(30, 145, 103),
"bile" => array(181, 195, 6),
"faded yellow" => array(254, 255, 127),
"very pale green" => array(207, 253, 188),
"vibrant green" => array(10, 221, 8),
"bright lime" => array(135, 253, 5),
"spearmint" => array(30, 248, 118),
"light aquamarine" => array(123, 253, 199),
"light sage" => array(188, 236, 172),
"yellowgreen" => array(187, 249, 15),
"baby poo" => array(171, 144, 4),
"dark seafoam" => array(31, 181, 122),
"deep teal" => array(0, 85, 90),
"heather" => array(164, 132, 172),
"rust orange" => array(196, 85, 8),
"dirty blue" => array(63, 130, 157),
"fern green" => array(84, 141, 68),
"bright lilac" => array(201, 94, 251),
"weird green" => array(58, 229, 127),
"peacock blue" => array(1, 103, 149),
"avocado green" => array(135, 169, 34),
"faded orange" => array(240, 148, 77),
"grape purple" => array(93, 20, 81),
"hot green" => array(37, 255, 41),
"lime yellow" => array(208, 254, 29),
"mango" => array(255, 166, 43),
"shamrock" => array(1, 180, 76),
"bubblegum" => array(255, 108, 181),
"purplish brown" => array(107, 66, 71),
"vomit yellow" => array(199, 193, 12),
"pale cyan" => array(183, 255, 250),
"key lime" => array(174, 255, 110),
"tomato red" => array(236, 45, 1),
"lightgreen" => array(118, 255, 123),
"merlot" => array(115, 0, 57),
"night blue" => array(4, 3, 72),
"purpleish pink" => array(223, 78, 200),
"apple" => array(110, 203, 60),
"baby poop green" => array(143, 152, 5),
"green apple" => array(94, 220, 31),
"heliotrope" => array(217, 79, 245),
"yellow/green" => array(200, 253, 61),
"almost black" => array(7, 13, 13),
"cool blue" => array(73, 132, 184),
"leafy green" => array(81, 183, 59),
"mustard brown" => array(172, 126, 4),
"dusk" => array(78, 84, 129),
"dull brown" => array(135, 110, 75),
"frog green" => array(88, 188, 8),
"vivid green" => array(47, 239, 16),
"bright light green" => array(45, 254, 84),
"fluro green" => array(10, 255, 2),
"kiwi" => array(156, 239, 67),
"seaweed" => array(24, 209, 123),
"navy green" => array(53, 83, 10),
"ultramarine blue" => array(24, 5, 219),
"iris" => array(98, 88, 196),
"pastel orange" => array(255, 150, 79),
"yellowish orange" => array(255, 171, 15),
"perrywinkle" => array(143, 140, 231),
"tealish" => array(36, 188, 168),
"dark plum" => array(63, 1, 44),
"pear" => array(203, 248, 95),
"pinkish orange" => array(255, 114, 76),
"midnight purple" => array(40, 1, 55),
"light urple" => array(179, 111, 246),
"dark mint" => array(72, 192, 114),
"greenish tan" => array(188, 203, 122),
"light burgundy" => array(168, 65, 91),
"turquoise blue" => array(6, 177, 196),
"ugly pink" => array(205, 117, 132),
"sandy" => array(241, 218, 122),
"electric pink" => array(255, 4, 144),
"muted purple" => array(128, 91, 135),
"mid green" => array(80, 167, 71),
"greyish" => array(168, 164, 149),
"neon yellow" => array(207, 255, 4),
"banana" => array(255, 255, 126),
"carnation pink" => array(255, 127, 167),
"tomato" => array(239, 64, 38),
"sea" => array(60, 153, 146),
"muddy brown" => array(136, 104, 6),
"turquoise green" => array(4, 244, 137),
"buff" => array(254, 246, 158),
"fawn" => array(207, 175, 123),
"muted blue" => array(59, 113, 159),
"pale rose" => array(253, 193, 197),
"dark mint green" => array(32, 192, 115),
"amethyst" => array(155, 95, 192),
"blue/green" => array(15, 155, 142),
"chestnut" => array(116, 40, 2),
"sick green" => array(157, 185, 44),
"pea" => array(164, 191, 32),
"rusty orange" => array(205, 89, 9),
"stone" => array(173, 165, 135),
"rose red" => array(190, 1, 60),
"pale aqua" => array(184, 255, 235),
"deep orange" => array(220, 77, 1),
"earth" => array(162, 101, 62),
"mossy green" => array(99, 139, 39),
"grassy green" => array(65, 156, 3),
"pale lime green" => array(177, 255, 101),
"light grey blue" => array(157, 188, 212),
"pale grey" => array(253, 253, 254),
"asparagus" => array(119, 171, 86),
"blueberry" => array(70, 65, 150),
"purple red" => array(153, 1, 71),
"pale lime" => array(190, 253, 115),
"greenish teal" => array(50, 191, 132),
"caramel" => array(175, 111, 9),
"deep magenta" => array(160, 2, 92),
"light peach" => array(255, 216, 177),
"milk chocolate" => array(127, 78, 30),
"ocher" => array(191, 155, 12),
"off green" => array(107, 163, 83),
"purply pink" => array(240, 117, 230),
"lightblue" => array(123, 200, 246),
"dusky blue" => array(71, 95, 148),
"golden" => array(245, 191, 3),
"light beige" => array(255, 254, 182),
"butter yellow" => array(255, 253, 116),
"dusky purple" => array(137, 91, 123),
"french blue" => array(67, 107, 173),
"ugly yellow" => array(208, 193, 1),
"greeny yellow" => array(198, 248, 8),
"orangish red" => array(244, 54, 5),
"shamrock green" => array(2, 193, 77),
"orangish brown" => array(178, 95, 3),
"tree green" => array(42, 126, 25),
"deep violet" => array(73, 6, 72),
"gunmetal" => array(83, 98, 103),
"blue/purple" => array(90, 6, 239),
"cherry" => array(207, 2, 52),
"sandy brown" => array(196, 166, 97),
"warm grey" => array(151, 138, 132),
"dark indigo" => array(31, 9, 84),
"midnight" => array(3, 1, 45),
"bluey green" => array(43, 177, 121),
"grey pink" => array(195, 144, 155),
"soft purple" => array(166, 111, 181),
"blood" => array(119, 0, 1),
"brown red" => array(146, 43, 5),
"medium grey" => array(125, 127, 124),
"berry" => array(153, 15, 75),
"poo" => array(143, 115, 3),
"purpley pink" => array(200, 60, 185),
"light salmon" => array(254, 169, 147),
"snot" => array(172, 187, 13),
"easter purple" => array(192, 113, 254),
"light yellow green" => array(204, 253, 127),
"dark navy blue" => array(0, 2, 46),
"drab" => array(130, 131, 68),
"light rose" => array(255, 197, 203),
"rouge" => array(171, 18, 57),
"purplish red" => array(176, 5, 75),
"slime green" => array(153, 204, 4),
"baby poop" => array(147, 124, 0),
"irish green" => array(1, 149, 41),
"pink/purple" => array(239, 29, 231),
"dark navy" => array(0, 4, 53),
"greeny blue" => array(66, 179, 149),
"light plum" => array(157, 87, 131),
"pinkish grey" => array(200, 172, 169),
"dirty orange" => array(200, 118, 6),
"rust red" => array(170, 39, 4),
"pale lilac" => array(228, 203, 255),
"orangey red" => array(250, 66, 36),
"primary blue" => array(8, 4, 249),
"kermit green" => array(92, 178, 0),
"brownish purple" => array(118, 66, 78),
"murky green" => array(108, 122, 14),
"wheat" => array(251, 221, 126),
"very dark purple" => array(42, 1, 52),
"bottle green" => array(4, 74, 5),
"watermelon" => array(253, 70, 89),
"deep sky blue" => array(13, 117, 248),
"fire engine red" => array(254, 0, 2),
"yellow ochre" => array(203, 157, 6),
"pumpkin orange" => array(251, 125, 7),
"pale olive" => array(185, 204, 129),
"light lilac" => array(237, 200, 255),
"lightish green" => array(97, 225, 96),
"carolina blue" => array(138, 184, 254),
"mulberry" => array(146, 10, 78),
"shocking pink" => array(254, 2, 162),
"auburn" => array(154, 48, 1),
"bright lime green" => array(101, 254, 8),
"celadon" => array(190, 253, 183),
"pinkish brown" => array(177, 114, 97),
"poo brown" => array(136, 95, 1),
"bright sky blue" => array(2, 204, 254),
"celery" => array(193, 253, 149),
"dirt brown" => array(131, 101, 57),
"strawberry" => array(251, 41, 67),
"dark lime" => array(132, 183, 1),
"copper" => array(182, 99, 37),
"medium brown" => array(127, 81, 18),
"muted green" => array(95, 160, 82),
"robin's egg" => array(109, 237, 253),
"bright aqua" => array(11, 249, 234),
"bright lavender" => array(199, 96, 255),
"ivory" => array(255, 255, 203),
"very light purple" => array(246, 206, 252),
"light navy" => array(21, 80, 132),
"pink red" => array(245, 5, 79),
"olive brown" => array(100, 84, 3),
"poop brown" => array(122, 89, 1),
"mustard green" => array(168, 181, 4),
"ocean green" => array(61, 153, 115),
"very dark blue" => array(0, 1, 51),
"dusty green" => array(118, 169, 115),
"light navy blue" => array(46, 90, 136),
"minty green" => array(11, 247, 125),
"adobe" => array(189, 108, 72),
"barney" => array(172, 29, 184),
"jade green" => array(43, 175, 106),
"bright light blue" => array(38, 247, 253),
"light lime" => array(174, 253, 108),
"dark khaki" => array(155, 143, 85),
"orange yellow" => array(255, 173, 1),
"ocre" => array(198, 156, 4),
"maize" => array(244, 208, 84),
"faded pink" => array(222, 157, 172),
"british racing green" => array(5, 72, 13),
"sandstone" => array(201, 174, 116),
"mud brown" => array(96, 70, 15),
"light sea green" => array(152, 246, 176),
"robin egg blue" => array(138, 241, 254),
"aqua marine" => array(46, 232, 187),
"dark sea green" => array(17, 135, 93),
"soft pink" => array(253, 176, 192),
"orangey brown" => array(177, 96, 2),
"cherry red" => array(247, 2, 42),
"burnt yellow" => array(213, 171, 9),
"brownish grey" => array(134, 119, 95),
"camel" => array(198, 159, 89),
"purplish grey" => array(122, 104, 127),
"marine" => array(4, 46, 96),
"greyish pink" => array(200, 141, 148),
"pale turquoise" => array(165, 251, 213),
"pastel yellow" => array(255, 254, 113),
"bluey purple" => array(98, 65, 199),
"canary yellow" => array(255, 254, 64),
"faded red" => array(211, 73, 78),
"sepia" => array(152, 94, 43),
"coffee" => array(166, 129, 76),
"bright magenta" => array(255, 8, 232),
"mocha" => array(157, 118, 81),
"ecru" => array(254, 255, 202),
"purpleish" => array(152, 86, 141),
"cranberry" => array(158, 0, 58),
"darkish green" => array(40, 124, 55),
"brown orange" => array(185, 105, 2),
"dusky rose" => array(186, 104, 115),
"melon" => array(255, 120, 85),
"sickly green" => array(148, 178, 28),
"silver" => array(197, 201, 199),
"purply blue" => array(102, 26, 238),
"purpleish blue" => array(97, 64, 239),
"hospital green" => array(155, 229, 170),
"shit brown" => array(123, 88, 4),
"mid blue" => array(39, 106, 179),
"amber" => array(254, 179, 8),
"easter green" => array(140, 253, 126),
"soft blue" => array(100, 136, 234),
"cerulean blue" => array(5, 110, 238),
"golden brown" => array(178, 122, 1),
"bright turquoise" => array(15, 254, 249),
"red pink" => array(250, 42, 85),
"red purple" => array(130, 7, 71),
"greyish brown" => array(122, 106, 79),
"vermillion" => array(244, 50, 12),
"russet" => array(161, 57, 5),
"steel grey" => array(111, 130, 138),
"lighter purple" => array(165, 90, 244),
"bright violet" => array(173, 10, 253),
"prussian blue" => array(0, 69, 119),
"slate green" => array(101, 141, 109),
"dirty pink" => array(202, 123, 128),
"dark blue green" => array(0, 82, 73),
"pine" => array(43, 93, 52),
"yellowy green" => array(191, 241, 40),
"dark gold" => array(181, 148, 16),
"bluish" => array(41, 118, 187),
"darkish blue" => array(1, 65, 130),
"dull red" => array(187, 63, 63),
"pinky red" => array(252, 38, 71),
"bronze" => array(168, 121, 0),
"pale teal" => array(130, 203, 178),
"military green" => array(102, 124, 62),
"barbie pink" => array(254, 70, 165),
"bubblegum pink" => array(254, 131, 204),
"pea soup green" => array(148, 166, 23),
"dark mustard" => array(168, 137, 5),
"shit" => array(127, 95, 0),
"medium purple" => array(158, 67, 162),
"very dark green" => array(6, 46, 3),
"dirt" => array(138, 110, 69),
"dusky pink" => array(204, 122, 139),
"red violet" => array(158, 1, 104),
"lemon yellow" => array(253, 255, 56),
"pistachio" => array(192, 250, 139),
"dull yellow" => array(238, 220, 91),
"dark lime green" => array(126, 189, 1),
"denim blue" => array(59, 91, 146),
"teal blue" => array(1, 136, 159),
"lightish blue" => array(61, 122, 253),
"purpley blue" => array(95, 52, 231),
"light indigo" => array(109, 90, 207),
"swamp green" => array(116, 133, 0),
"brown green" => array(112, 108, 17),
"dark maroon" => array(60, 0, 8),
"hot purple" => array(203, 0, 245),
"dark forest green" => array(0, 45, 4),
"faded blue" => array(101, 140, 187),
"drab green" => array(116, 149, 81),
"light lime green" => array(185, 255, 102),
"snot green" => array(157, 193, 0),
"yellowish" => array(250, 238, 102),
"light blue green" => array(126, 251, 179),
"bordeaux" => array(123, 0, 44),
"light mauve" => array(194, 146, 161),
"ocean" => array(1, 123, 146),
"marigold" => array(252, 192, 6),
"muddy green" => array(101, 116, 50),
"dull orange" => array(216, 134, 59),
"steel" => array(115, 133, 149),
"electric purple" => array(170, 35, 255),
"fluorescent green" => array(8, 255, 8),
"yellowish brown" => array(155, 122, 1),
"blush" => array(242, 158, 142),
"soft green" => array(111, 194, 118),
"bright orange" => array(255, 91, 0),
"lemon" => array(253, 255, 82),
"purple grey" => array(134, 111, 133),
"acid green" => array(143, 254, 9),
"pale lavender" => array(238, 207, 254),
"violet blue" => array(81, 10, 201),
"light forest green" => array(79, 145, 83),
"burnt red" => array(159, 35, 5),
"khaki green" => array(114, 134, 57),
"cerise" => array(222, 12, 98),
"faded purple" => array(145, 110, 153),
"apricot" => array(255, 177, 109),
"dark olive green" => array(60, 77, 3),
"grey brown" => array(127, 112, 83),
"green grey" => array(119, 146, 111),
"true blue" => array(1, 15, 204),
"pale violet" => array(206, 174, 250),
"periwinkle blue" => array(143, 153, 251),
"light sky blue" => array(198, 252, 255),
"blurple" => array(85, 57, 204),
"green brown" => array(84, 78, 3),
"bluegreen" => array(1, 122, 121),
"bright teal" => array(1, 249, 198),
"brownish yellow" => array(201, 176, 3),
"pea soup" => array(146, 153, 1),
"forest" => array(11, 85, 9),
"barney purple" => array(160, 4, 152),
"ultramarine" => array(32, 0, 177),
"purplish" => array(148, 86, 140),
"puke yellow" => array(194, 190, 14),
"bluish grey" => array(116, 139, 151),
"dark periwinkle" => array(102, 95, 209),
"dark lilac" => array(156, 109, 165),
"reddish" => array(196, 66, 64),
"light maroon" => array(162, 72, 87),
"dusty purple" => array(130, 95, 135),
"terra cotta" => array(201, 100, 59),
"avocado" => array(144, 177, 52),
"marine blue" => array(1, 56, 106),
"teal green" => array(37, 163, 111),
"slate grey" => array(89, 101, 109),
"lighter green" => array(117, 253, 99),
"electric green" => array(33, 252, 13),
"dusty blue" => array(90, 134, 173),
"golden yellow" => array(254, 198, 21),
"bright yellow" => array(255, 253, 1),
"light lavender" => array(223, 197, 254),
"umber" => array(178, 100, 0),
"poop" => array(127, 94, 0),
"dark peach" => array(222, 126, 93),
"jungle green" => array(4, 130, 67),
"eggshell" => array(255, 255, 212),
"denim" => array(59, 99, 140),
"yellow brown" => array(183, 148, 0),
"dull purple" => array(132, 89, 126),
"chocolate brown" => array(65, 25, 0),
"wine red" => array(123, 3, 35),
"neon blue" => array(4, 217, 255),
"dirty green" => array(102, 126, 44),
"light tan" => array(251, 238, 172),
"ice blue" => array(215, 255, 254),
"cadet blue" => array(78, 116, 150),
"dark mauve" => array(135, 76, 98),
"very light blue" => array(213, 255, 255),
"grey purple" => array(130, 109, 140),
"pastel pink" => array(255, 186, 205),
"very light green" => array(209, 255, 189),
"dark sky blue" => array(68, 142, 228),
"evergreen" => array(5, 71, 42),
"dull pink" => array(213, 134, 157),
"aubergine" => array(61, 7, 52),
"mahogany" => array(74, 1, 0),
"reddish orange" => array(248, 72, 28),
"deep green" => array(2, 89, 15),
"vomit green" => array(137, 162, 3),
"purple pink" => array(224, 63, 216),
"dusty pink" => array(213, 138, 148),
"faded green" => array(123, 178, 116),
"camo green" => array(82, 101, 37),
"pinky purple" => array(201, 76, 190),
"pink purple" => array(219, 75, 218),
"brownish red" => array(158, 54, 35),
"dark rose" => array(181, 72, 93),
"mud" => array(115, 92, 18),
"brownish" => array(156, 109, 87),
"emerald green" => array(2, 143, 30),
"pale brown" => array(177, 145, 110),
"dull blue" => array(73, 117, 156),
"burnt umber" => array(160, 69, 14),
"medium green" => array(57, 173, 72),
"clay" => array(182, 106, 80),
"light aqua" => array(140, 255, 219),
"light olive green" => array(164, 190, 92),
"brownish orange" => array(203, 119, 35),
"dark aqua" => array(5, 105, 107),
"purplish pink" => array(206, 93, 174),
"dark salmon" => array(200, 90, 83),
"greenish grey" => array(150, 174, 141),
"jade" => array(31, 167, 116),
"ugly green" => array(122, 151, 3),
"dark beige" => array(172, 147, 98),
"emerald" => array(1, 160, 73),
"pale red" => array(217, 84, 77),
"light magenta" => array(250, 95, 247),
"sky" => array(130, 202, 252),
"light cyan" => array(172, 255, 252),
"yellow orange" => array(252, 176, 1),
"reddish purple" => array(145, 9, 81),
"reddish pink" => array(254, 44, 84),
"orchid" => array(200, 117, 196),
"dirty yellow" => array(205, 197, 10),
"orange red" => array(253, 65, 30),
"deep red" => array(154, 2, 0),
"orange brown" => array(190, 100, 0),
"cobalt blue" => array(3, 10, 167),
"neon pink" => array(254, 1, 154),
"rose pink" => array(247, 135, 154),
"greyish purple" => array(136, 113, 145),
"raspberry" => array(176, 1, 73),
"aqua green" => array(18, 225, 147),
"salmon pink" => array(254, 123, 124),
"tangerine" => array(255, 148, 8),
"brownish green" => array(106, 110, 9),
"red brown" => array(139, 46, 22),
"greenish brown" => array(105, 97, 18),
"pumpkin" => array(225, 119, 1),
"pine green" => array(10, 72, 30),
"charcoal" => array(52, 56, 55),
"baby pink" => array(255, 183, 206),
"cornflower" => array(106, 121, 247),
"blue violet" => array(93, 6, 233),
"chocolate" => array(61, 28, 2),
"greyish green" => array(130, 166, 125),
"scarlet" => array(190, 1, 25),
"green yellow" => array(201, 255, 39),
"dark olive" => array(55, 62, 2),
"sienna" => array(169, 86, 30),
"pastel purple" => array(202, 160, 255),
"terracotta" => array(202, 102, 65),
"aqua blue" => array(2, 216, 233),
"sage green" => array(136, 179, 120),
"blood red" => array(152, 0, 2),
"deep pink" => array(203, 1, 98),
"grass" => array(92, 172, 45),
"moss" => array(118, 153, 88),
"pastel blue" => array(162, 191, 254),
"bluish green" => array(16, 166, 116),
"green blue" => array(6, 180, 139),
"dark tan" => array(175, 136, 74),
"greenish blue" => array(11, 139, 135),
"pale orange" => array(255, 167, 86),
"vomit" => array(162, 164, 21),
"forrest green" => array(21, 68, 6),
"dark lavender" => array(133, 103, 152),
"dark violet" => array(52, 1, 63),
"purple blue" => array(99, 45, 233),
"dark cyan" => array(10, 136, 138),
"olive drab" => array(111, 118, 50),
"pinkish" => array(212, 106, 126),
"cobalt" => array(30, 72, 143),
"neon purple" => array(188, 19, 254),
"light turquoise" => array(126, 244, 204),
"apple green" => array(118, 205, 38),
"dull green" => array(116, 166, 98),
"wine" => array(128, 1, 63),
"powder blue" => array(177, 209, 252),
"off white" => array(255, 255, 228),
"electric blue" => array(6, 82, 255),
"dark turquoise" => array(4, 92, 90),
"blue purple" => array(87, 41, 206),
"azure" => array(6, 154, 243),
"bright red" => array(255, 0, 13),
"pinkish red" => array(241, 12, 69),
"cornflower blue" => array(81, 112, 215),
"light olive" => array(172, 191, 105),
"grape" => array(108, 52, 97),
"greyish blue" => array(94, 129, 157),
"purplish blue" => array(96, 30, 249),
"yellowish green" => array(176, 221, 22),
"greenish yellow" => array(205, 253, 2),
"medium blue" => array(44, 111, 187),
"dusty rose" => array(192, 115, 122),
"light violet" => array(214, 180, 252),
"midnight blue" => array(2, 0, 53),
"bluish purple" => array(112, 59, 231),
"red orange" => array(253, 60, 6),
"dark magenta" => array(150, 0, 86),
"greenish" => array(64, 163, 104),
"ocean blue" => array(3, 113, 156),
"coral" => array(252, 90, 80),
"cream" => array(255, 255, 194),
"reddish brown" => array(127, 43, 10),
"burnt sienna" => array(176, 78, 15),
"brick" => array(160, 54, 35),
"sage" => array(135, 174, 115),
"grey green" => array(120, 155, 115),
"white" => array(255, 255, 255),
"robin's egg blue" => array(152, 239, 249),
"moss green" => array(101, 139, 56),
"steel blue" => array(90, 125, 154),
"eggplant" => array(56, 8, 53),
"light yellow" => array(255, 254, 122),
"leaf green" => array(92, 169, 4),
"light grey" => array(216, 220, 214),
"puke" => array(165, 165, 2),
"pinkish purple" => array(214, 72, 215),
"sea blue" => array(4, 116, 149),
"pale purple" => array(183, 144, 212),
"slate blue" => array(91, 124, 153),
"blue grey" => array(96, 124, 142),
"hunter green" => array(11, 64, 8),
"fuchsia" => array(237, 13, 217),
"crimson" => array(140, 0, 15),
"pale yellow" => array(255, 255, 132),
"ochre" => array(191, 144, 5),
"mustard yellow" => array(210, 189, 10),
"light red" => array(255, 71, 76),
"cerulean" => array(4, 133, 209),
"pale pink" => array(255, 207, 220),
"deep blue" => array(4, 2, 115),
"rust" => array(168, 60, 9),
"light teal" => array(144, 228, 193),
"slate" => array(81, 101, 114),
"goldenrod" => array(250, 194, 5),
"dark yellow" => array(213, 182, 10),
"dark grey" => array(54, 55, 55),
"army green" => array(75, 93, 22),
"grey blue" => array(107, 139, 164),
"seafoam" => array(128, 249, 173),
"puce" => array(165, 126, 82),
"spring green" => array(169, 249, 113),
"dark orange" => array(198, 81, 2),
"sand" => array(226, 202, 118),
"pastel green" => array(176, 255, 157),
"mint" => array(159, 254, 176),
"light orange" => array(253, 170, 72),
"bright pink" => array(254, 1, 177),
"chartreuse" => array(193, 248, 10),
"deep purple" => array(54, 1, 63),
"dark brown" => array(52, 28, 2),
"taupe" => array(185, 162, 129),
"pea green" => array(142, 171, 18),
"puke green" => array(154, 174, 7),
"kelly green" => array(2, 171, 46),
"seafoam green" => array(122, 249, 171),
"blue green" => array(19, 126, 109),
"khaki" => array(170, 166, 98),
"burgundy" => array(97, 0, 35),
"dark teal" => array(1, 77, 78),
"brick red" => array(143, 20, 2),
"royal purple" => array(75, 0, 110),
"plum" => array(88, 15, 65),
"mint green" => array(143, 255, 159),
"gold" => array(219, 180, 12),
"baby blue" => array(162, 207, 254),
"yellow green" => array(192, 251, 45),
"bright purple" => array(190, 3, 253),
"dark red" => array(132, 0, 0),
"pale blue" => array(208, 254, 254),
"grass green" => array(63, 155, 11),
"navy" => array(1, 21, 62),
"aquamarine" => array(4, 216, 178),
"burnt orange" => array(192, 78, 1),
"neon green" => array(12, 255, 12),
"bright blue" => array(1, 101, 252),
"rose" => array(207, 98, 117),
"light pink" => array(255, 209, 223),
"mustard" => array(206, 179, 1),
"indigo" => array(56, 2, 130),
"lime" => array(170, 255, 50),
"sea green" => array(83, 252, 161),
"periwinkle" => array(142, 130, 254),
"dark pink" => array(203, 65, 107),
"olive green" => array(103, 122, 4),
"peach" => array(255, 176, 124),
"pale green" => array(199, 253, 181),
"light brown" => array(173, 129, 80),
"hot pink" => array(255, 2, 141),
"black" => array(0, 0, 0),
"lilac" => array(206, 162, 253),
"navy blue" => array(0, 17, 70),
"royal blue" => array(5, 4, 170),
"beige" => array(230, 218, 166),
"salmon" => array(255, 121, 108),
"olive" => array(110, 117, 14),
"maroon" => array(101, 0, 33),
"bright green" => array(1, 255, 7),
"dark purple" => array(53, 6, 62),
"mauve" => array(174, 113, 129),
"forest green" => array(6, 71, 12),
"aqua" => array(19, 234, 201),
"cyan" => array(0, 255, 255),
"tan" => array(209, 178, 111),
"dark blue" => array(0, 3, 91),
"lavender" => array(199, 159, 239),
"turquoise" => array(6, 194, 172),
"dark green" => array(3, 53, 0),
"violet" => array(154, 14, 234),
"light purple" => array(191, 119, 246),
"lime green" => array(137, 254, 5),
"grey" => array(146, 149, 145),
"gray" => array(146, 149, 145),
"sky blue" => array(117, 187, 253),
"yellow" => array(255, 255, 20),
"magenta" => array(194, 0, 120),
"light green" => array(150, 249, 123),
"orange" => array(249, 115, 6),
"teal" => array(2, 147, 134),
"light blue" => array(149, 208, 252),
"red" => array(229, 0, 0),
"brown" => array(101, 55, 0),
"pink" => array(255, 129, 192),
"blue" => array(3, 67, 223),
"green" => array(21, 176, 26),
"purple" => array(126, 30, 156),
);
foreach ($colors as $key=>$val)
{
	if ($key==$colorName)
	{
		return $this->rgb2html($val[0], $val[1], $val[2]); 
	}
}
return $colorName; 
   }
   
   function getColors($cssfiles, &$retcolors)
   {
	   foreach ($cssfiles as $file)
	   {
		   $data = file_get_contents($file); 
		   $m = array(); 
		   preg_match_all('((#([0-9A-Fa-f]{3,6})\b)|(aqua)|(black)|(blue)|(fuchsia)|(gray)|(green)|(lime)|(maroon)|(navy)|(olive)|(orange)|(purple)|(red)|(silver)|(teal)|(white)|(yellow)|(rgb\(\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*\))|(rgb\(\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*\)))', $data, $m, PREG_OFFSET_CAPTURE); 
	$colors = array(); 
    foreach ($m as $a)
	foreach ($a as $y)
	{
		if (empty($y[0]))
		{
			continue; 
		}
		
		$color = $y[0]; 
		$color = str_replace('#', '', $color); 
		$color = trim($color); 
		$color = strtolower($color); 
		
		$color = $this->getColorCode($color); 
		$color = str_replace('#', '', $color); 
		$color = $this->getRealColor($color); 
		$color = str_replace('#', '', $color); 
		if (strlen($color)==3) $color = $color.$color; 
		if (substr($color, 0,1)!='#') $color = '#'.$color; 
		if (!empty($color))
	    {
		if (!isset($retcolors[$color])) $retcolors[$color] = array(); 
		$retcolors[$color][$y[0]] = $y[0]; 
	    }
		
		
			
	}
	

	   }
	   
	   
	   
	   
	   // sort colors: 
	  // $id=0;
	$array=array();
	$colors = array(); 
	foreach ($retcolors as $color => $val)
	{
	if ($color !== '#')
	$array[$color]=count($val);
	//$colors[$id]=$color;
	//$id++;
	}
	
	$new = array(); 
	arsort($array); 
	
	foreach ($array as $color => $count)
	{
	  $new[$color] = $retcolors[$color]; 	
	}
	
	
	$retcolors = $new; 
	return;
	/*
	var_dump($retcolors); die(); 
	var_dump($array); die(); 
	foreach($array as $i => $value){
        $j = $i + 1;
        while (($j < count($array) && ($array[$j] < $array[$i])))
        {
            $tmp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $tmp;
			$tmpColor = $colors[$i];
			$colors[$i] = $colors[$j];
			$colors[$j] = $tmpColor;
			if ($i > 0)
            {
                $i--;
            }
            $j--;
        }
	}
	  */ 
	   
   }
   
   function rgb2hex($R, $G, $B)
   {
 $R = (int)$R; 
 $G = (int)$G; 
 $B = (int)$B; 
 
 $R=dechex($R);
 If (strlen($R)<2)
 $R='0'.$R;
 
  $G=dechex($G);
 If (strlen($G)<2)
 $G='0'.$G;
 
 $B=dechex($B);
 If (strlen($B)<2)
 $B='0'.$B;
 
 return $R . $G . $B;
 

}
   
   function getRealColor($c)
   {
   
      $c = str_replace('#', '', $c); 
	  
	  if (ctype_xdigit($c))
	   {
	      if (strlen($c)==6) return $c; 
		  if (strlen($c)==3) return $c.$c; 
	   }
	   
	  if (stripos($c, 'rgb')!==false)
	   {
	   
	      $x1 = stripos($c, '('); 
		  $x2 = stripos($c, ')', $x1); 
		  if (($x1 !== false) && ($x2 !== false))
		  {
		  
		     $colors = substr($c, $x1+1, $x2-$x1-1); 
		     $a = explode(',', $colors); 
			 
			 
			 if (count($a)>=3)
			  {
			     return $this->rgb2hex($a[0], $a[1], $a[2]); 
			  }
			 
			 
		  }
		  
	   }
	   // incorrect color: 
	   return ''; 
	   
   }
   function getPrefered()
   {
	   
	   
$db = JFactory::getDBO(); 
$q = 'select template from #__template_styles where home = 1 and client_id = 0 limit 0,1'; 
$db->setQuery($q); 
$template = $db->loadResult(); 
	  
		  $colors2 = OPCcache::get('css_'.$template); 
		  if (!empty($colors2)) { $colors = $colors2; return $colors; }
		  


	   $templateDir = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR. $template;
       if (!file_exists($templateDir)) {
	   return; }
	   $files = JFolder::files($templateDir, '.css', true, true, array() );
	   $colors = array(); 
	   $this->getColors($files, $colors); 
	   OPCcache::store($colors, 'css_'.$template); 
	   return $colors; 
	   

   }
   
   function updateColors()
   {
	   	jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		$data = JRequest::get('post'); 
		$colorft = array(); 
		foreach ($data as $key=>$val)
		{
			if (stripos($key, 'fromcolor')===0)
			{
				//colors += '&fromcolor_'+op_escape(origColors[i])+'_tocolor_'+op_escape(val)+'=1';  
				$a = explode('_tocolor_', $key);
				$from = str_replace('fromcolor_', '', $a[0]);
			    $to = $a[1];
				
				if (!ctype_xdigit($from) || (!ctype_xdigit($to))) continue; 
				$colorft[$from] = $to; 
			}
		}
		if (empty($colorft)) return;
				
				   $cssfiles = $this->getCss(); 
				   $getColors = array(); 
				   $this->getColors($cssfiles, $getColors ); 
				   
				   $count = $count1 = $count2 = $count3 = $count4 = $count5 = $count6 = $count7 = 0; 
				   foreach ($cssfiles as $file)
				   {
					   $data = file_get_contents($file); 
					   foreach ($getColors as $orig=>$var)
					   {
						   $orig = str_replace('#', '', $orig); 
						   if (!empty($colorft[$orig]))
						   {
							  
							   foreach ($var as $variation)
							   {
								   
								   if (substr($variation, 0,1)=='#')
								   {
									
								   // will replace all of the occurances with the new value
									$count1 = 0; 
								   $data = str_replace($variation, '#'.$colorft[$orig], $data, $count1);
								   echo 'replacing '.$variation.' with '.'#'.$colorft[$orig]."<br />\n"; 
								   }
								   else
								   {
									   if (ctype_xdigit($variation))
									   {
										   echo 'replacing #'.$variation.' with '.$colorft[$orig]."<br />\n"; 
										   $count2 = 0; 
										   $data = str_replace('#'.$variation, '#'.$colorft[$orig], $data, $count2);
									   }
									   else
									   {
										   $vn = strtolower($variation); 
										   $color = $this->getColorCode($vn); 
										   if (!empty($color))
										   {
											  echo 'replacing '.$variation.' with #'.$colorft[$orig]."<br />\n"; 
											  // this is dangerous, so we need to find it better:
											  // with a space
											  $count3 = 0; 
											  $data = str_replace($variation.' ', '#'.$colorft[$orig].' ', $data, $count3);
											  // width ;
											  $count4 = 0; 
											  $data = str_replace($variation.';', '#'.$colorft[$orig].';', $data, $count4);
											  // with end line like \r\r\n or \r\n
											  $count5 = 0; 
											  $data = str_replace($variation."\r", '#'.$colorft[$orig]."\r", $data, $count5);
											  // with simple endline \n
											  $count6 = 0; 
											  $data = str_replace($variation."\n", '#'.$colorft[$orig]."\n", $data, $count6);
										   }
										   else
										   {
											  echo 'replacing #'.$variation.' with #'.$colorft[$orig]."<br />\n"; 
											  $count7 = 0; 
											  $data = str_replace('#'.$variation, '#'.$colorft[$orig], $data, $count, $count7);
											   
										   }
										   
									   }
								   }
								  
								   
							   }
							    $count += $count1 + $count2 + $count3 + $count4 + $count5 + $count6 + $count7; 
							    
							// remove BOM: 
						    $data = str_replace("\r\r\n", "\r\n", $data); 
							$data = str_replace("\xEF\xBB\xBF", "", $data); 
							echo 'Writing to: '.$file."<br />\n"; 
							JFile::write($file, $data); 
							   
						   }
					   }
				   }
					echo 'END_DEBUG'; 
					//die('here');    
					   
				  
				}
   function createCustom()
   {
     	  
		  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		  $msg = ''; 
		  
		  if (stripos($selected_template, '_custom')!==false)
			  $suffix = ''; 
		  else
		  $suffix = '_custom'; 
		  
		  
		  if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix))
		  {
		  if (JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix)===false)
		  {
			$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix).'<br />'; 
		  }
		  }
		  else
		  {
			  $time = time(); 
			if (JFolder::copy(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_backup_'.$time, '', true )===false)
			{
				$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_backup_'.$time, JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes"); 
				
			}
		  }
		  // theme already exists: 
		  if (empty($msg))
		  if (JFolder::copy(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix, '', true )===false)
		  {
			$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', '', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix).'<br />'; 
		  }
		  //var_dump($msg); die(); 
		  JFolder::delete(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.'_preview'); 
		  echo $msg; 
		  if (empty($msg))
		  {
		  echo JText::sprintf('COM_ONEPAGE_CREATED_CUSTOM_THEME', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.$suffix);
	      echo 'OPC_REDIRECT'; 
		  }
		  echo 'OPC_DISPLAY_POPUP'; 
	   
   }
   
			
		
   
   
}
