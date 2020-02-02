<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::stylesheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css'); 
JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
JHTML::script('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js'); 


$vmlimit = JRequest::getVar('vmlimit', 0); 
if (!empty($vmlimit)) { 
?>
<br />

<?php 
$link = 'index.php?option=com_virtuemart&view=orders'; 
$vmlimit = (int)JRequest::getVar('vmlimit', 0);
if (!empty($vmlimit)) $link .= '&limit='.$vmlimit; 

$vmlimitstart = (int)JRequest::getVar('vmlimitstart', 0);
if (!empty($vmlimitstart)) $link .= '&limitstart='.$vmlimitstart; 

$vmsearch = JRequest::getVar('vmsearch', '');
if (!empty($vmsearch)) $link .= '&search='.urlencode($vmsearch); 

?>
<a class="uk-button uk-button-primary" href="<?php echo $link; ?>">Return to VM order manager...</a>

<?php
}
else { 
?><br /><a class="uk-button uk-button-primary" href="index.php?option=com_onepage&view=orders">Return to OPC order manager...</a>

<?php 

}