<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
defined('_JEXEC') or die;
JToolBarHelper::Title(JText::_('COM_ONEPAGE_THEME_CONFIG') , 'generic.png');
JToolBarHelper::apply();

JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
    
?>
<form action="<?php echo JURI::base(); ?>index.php?option=com_onepage&amp;controller=themeconfig" method="post" name="adminForm" id="adminForm">
<?php
if (!empty($this->form))
		{
		
		if (!empty($this->form['description']))
		{
		
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.JText::_('JGLOBAL_DESCRIPTION').'</legend>';
		echo '<p>'.$this->form['description'].'</p>';
		echo '</fieldset>'; 
		}
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.$this->form['title'].'</legend>'; 
		echo $this->form['params']; 
	    echo '</fieldset>'; 
	 

		
		}
?>

	
	<input type="hidden" name="<?php if (method_exists('JUtility', 'getToken'))
	echo JUtility::getToken();
	else echo JSession::getFormToken(); ?>" value="1" />
	
			<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="themeconfig" />
		<input type="hidden" name="task" id="task" value="apply" />
		<input type="hidden" name="task2" id="task2" value="apply" />



	
</form>		