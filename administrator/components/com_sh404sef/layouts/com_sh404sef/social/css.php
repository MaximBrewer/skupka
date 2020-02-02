<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

/**
 * Input:
 * 
 * None
 * 
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

?>

<!-- sh404SEF social buttons css -->
<style type="text/css">
/* Top padding for buttons set */
div.sh404sef-social-buttons {
	padding-top: 0.5em;
}
.sh404sef-social-buttons span {
   	display: inline-block;
 	vertical-align: top;
 	margin-bottom: 0.3em;
}
/* fix for Linkedin, not full fix as Linkedin has some inline style with !important, which can't be overriden */
.sh404sef-social-buttons span.IN-widget[style] {
/*  	vertical-align: bottom !important; */
}
/* vertical adjustment for Linkedin */
.sh404sef-social-buttons span.linkedin {
/*     position: relative; */
/*     top: 3px; */
}
/* vertical adjustment for Google+ page */
.sh404sef-social-buttons span.googlepluspage {
/*     position: relative; */
/*      top: 2px; */
}

/* Facebook flyout cut-off fix */
.fb-like span{overflow:visible !important; } 
.fb-send span{overflow:visible !important;}
.fb-like iframe{max-width: none !important; } 
.fb-send iframe{max-width: none !important; }

/* Joomla default templates css fix */
/* parent elements needs to have overflow visible */
.items-row.cols-2, .items-leading {overflow:visible !important;}
#contentarea {overflow:visible !important;}

</style>
<!-- End of sh404SEF social buttons css -->
