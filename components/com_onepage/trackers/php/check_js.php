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
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
if (class_exists('JHTMLOPC'))
JHTMLOPC::script('opcping.js', 'components/com_onepage/assets/js/', false);

?><script type="text/javascript">
/* <![CDATA[ */
var ping_url = '<?php echo $this->pingUrl; ?>'; 
var ping_data = '<?php echo $this->pingData; ?>'; 
if (typeof opc_pingDone != 'undefined')
opc_pingDone(ping_url, ping_data); 
/* ]]> */
</script>
