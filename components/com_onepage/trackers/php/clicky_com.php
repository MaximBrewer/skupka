<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
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
$order_total = $this->order['details']['BT']->order_total;

?>
<script type="text/javascript">
/* <![CDATA[ */
var clicky_custom = {
goal: { name: "<?php echo $this->escapeDouble($this->params->goal_name); ?>, <?php echo $this->escapeDouble(JText::_($this->order['details']['BT']->order_status_name)); ?>,Order-Nr. <?php echo $this->escapeDouble($this->order['details']['BT']->virtuemart_order_id); ?>,  <?php echo $this->escapeDouble($this->order['details']['BT']->order_number); ?>"<?php 

if (!empty($this->params->custom_goal_id))
{
  ?>, id: "<?php echo $this->params->custom_goal_id; ?>", revenue: "<?php echo number_format($order_total, 2, '.', ''); ?>"<?php
}

?> }, <?php 
$user = JFactory::getUser(); 
$email = $user->get('email'); 
$username = $user->get('username'); 

if (!empty($email))
if (!empty($this->params->allow_visitor_data)) { 
?>

visitor: {
username: '<?php echo $this->escapeSingle($username); ?>',
email: '<?php echo $this->escapeSingle($email); ?>' 
}
<?php } ?>
};  
var clicky_goal = { id: "<?php echo $this->params->goal_id; ?>" };
<?php 
/*
$user = JFactory::getUser(); 
$email = $user->get('email'); 
$username = $user->get('username'); 

if (!empty($email))
if (!empty($this->params->allow_visitor_data)) { 
?>
var clicky_custom = clicky_custom || {};
  clicky_custom.visitor = {
    username: '<?php echo $this->escapeSingle($username); ?>',
    email: '<?php echo $this->escapeSingle($email); ?>'
  };
<?php
}  
*/
?>

var clicky_site_ids = clicky_site_ids || [];
clicky_site_ids.push('<?php echo $this->params->site_id; ?>');
(function() {
var s = document.createElement('script');
s.type = 'text/javascript';
s.async = true;
s.src = '//static.getclicky.com/js';
( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
})();
/* ]]> */
</script>
<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/<?php echo $this->params->site_id; ?>ns.gif" /></p></noscript>

