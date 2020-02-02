<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This loads before first ajax call is done, this file is called per each shipping html generated
*/
defined('_JEXEC') or die('Restricted access');
// local variable: $html

if (stripos($html, 'zasilkovna_select')!==false)
{
  
  
  
  $new_html = ''; //'<script type="text/javascript">opc_zasPlace();</script>'; 
  $html = str_replace('packetery-branch-list', ' renamed ', $html); 
  $html = str_replace('Načítání: seznam poboček osobního odběru', $new_html, $html); 
}