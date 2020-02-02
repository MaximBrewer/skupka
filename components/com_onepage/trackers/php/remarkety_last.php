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

$this->isPureJavascript = false; 

$email = JFactory::getUser()->get('email', ''); 
$cart = VirtuemartCart::getCart(); 
if (empty($email))
if ((!empty($cart->BT)) && (!empty($cart->BT['email']))) $email = $cart->BT['email']; 

if (!empty($email)) {
?>
<script>
        var _rmData = _rmData || [];
        _rmData.push(["setCustomer", "<?php echo $this->escapeDouble($email); ?>"]);
</script>
		
<?php 
}

?>

<script>
            var _rmData = _rmData || [];
            _rmData.push(['setStoreKey', '<?php echo $this->params->storeKey; ?>']);
</script>
<script>(function(d, t) {
          var g = d.createElement(t),
              s = d.getElementsByTagName(t)[0];
          g.src = '<?php echo $this->params->trackscripturl; ?>';
          s.parentNode.insertBefore(g, s);
        }(document, 'script'));
</script>


<script>
if ((typeof console != 'undefined') && (typeof console.log == 'function')) {
 console.log('OPC Tracking: Remarkety Loaded', _rmData); 
}
</script>		