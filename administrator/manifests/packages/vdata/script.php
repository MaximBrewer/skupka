<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of vData package
 */
class pkg_vdataInstallerScript
{
	var $messages;
	var $status;
	var	$sourcePath;

	function execute()
	{

		//get version number from manifest file.
		$jinstaller	= JInstaller::getInstance();
		$installer = new VdataInstaller( $jinstaller );
		$installer->execute();

		$this->messages	= $installer->getMessages();
	}
	
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		
		
		
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		
		
		
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		$db = JFactory::getDbo(); 
		$query = $db->getQuery(true);
		$fields = array($db->quoteName('type').'=2');
		$query->update($db->quoteName('#__vd_schedules'))->set($fields)->where($db->quoteName('iotype'). '=2' );
		$db->setQuery($query);
		try 
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			JError::raiseWarning(null, JText::_('Following error occured<br />'). $e->getMessage());
			return false;
		}
		$query->clear();
		
		$fields1 = array($db->quoteName('iotype').'=1');
		$query->update($db->quoteName('#__vd_schedules'))->set($fields1)->where($db->quoteName('iotype'). '=2' );
		$db->setQuery($query);
		try 
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			JError::raiseWarning(null, JText::_('Following error occured<br />'). $e->getMessage());
			return false;
		}
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
		$newRelease = $parent->get( "manifest" )->version;
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $newRelease;
			if ( version_compare( $newRelease, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
			if(version_compare($oldRelease, '2.0.6', 'le') && version_compare($newRelease, '2.1.0', 'ge')){
				
				$db = JFactory::getDbo();
				$db->setQuery('TRUNCATE TABLE #__vd_schedules');
				$result = $db->execute();
				$db->setQuery('TRUNCATE TABLE #__vd_profiles');
				$result = $db->execute();
			}
			
		}
		else {
			
		}
		
	}
	
	function getParam( $name ) {
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_vdata"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
	
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	 
	function postflight($type, $parent) {
		
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		if($type=="install")	{
				
			$fields1 = array(
				$db->quoteName('position') . ' = ' . $db->quote('cpanel'),
				$db->quoteName('published') . ' = 1',
				$db->quoteName('ordering'). ' = 0'
			);
			$conditions1 = array(
				$db->quoteName('module') . ' = '.$db->quote('mod_vdata_icons')
			);
			$query->update($db->quoteName('#__modules'))->set($fields1)->where($conditions1);
			$db->setQuery($query);
			$result = $db->execute();
			$query->clear();
		
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__modules'));
			$query->where($db->quoteName('module').' = '.$db->quote('mod_vdata_icons'));
			$db->setQuery($query);
			$mod_icon_id = (int)$db->loadResult();	
			$query->clear();
			
			// $db->setQuery("INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (".$mod_icon_id.", 0)");
            $query->insert($db->quoteName('#__modules_menu'))
				->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
				->values(implode(',',array($mod_icon_id, 0)));
			$db->setQuery($query);
			$db->execute();
			$query->clear();
			
			$fields2 = array(
				$db->quoteName('position') . ' = ' . $db->quote('menu'),
				$db->quoteName('published') . ' = 1',
				$db->quoteName('ordering'). ' = 1'
			);
			$conditions2= array(
				$db->quoteName('module') . ' = '.$db->quote('mod_vdata_admin_menu')
			);
			$query->update($db->quoteName('#__modules'))->set($fields2)->where($conditions2);
			$db->setQuery($query);
			$result = $db->execute();
			$query->clear();
			
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__modules'));
			$query->where($db->quoteName('module').' = '.$db->quote('mod_vdata_admin_menu'));
			$db->setQuery($query);
			$id = (int)$db->loadResult();
			$query->clear();
			
			// $db->setQuery("INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (".$id.", 0)");
			$query->insert($db->quoteName('#__modules_menu'))
				->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
				->values(implode(',',array($id, 0)));
			$db->setQuery($query);
            $db->execute();
			$query->clear();
			
			// retrieving vData custom plugin id
			$query->select($db->quoteName('extension_id'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element').' = '.$db->quote('custom'));
			$query->where($db->quoteName('folder').' = '.$db->quote('vdata'));
			$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
			$query->setLimit('1');
			$db->setQuery( $query );
			$pluginid = (int)$db->loadResult();
			$query->clear();
			
			// enabling vData custom plugin by default
			$query->update($db->quoteName('#__extensions'))->set('enabled = 1')->where($db->quoteName('extension_id').' = '.$db->quote((int)$pluginid));
			$db->setQuery( $query );
			$db->execute();
			echo $db->getErrorMsg();
			$query->clear();
			
			// updating vData custom plugin id in vData profiles table
			$query->update($db->quoteName('#__vd_profiles'))->set('pluginid='.$pluginid);
			$db->setQuery( $query );
			$db->execute();
			$query->clear();
			echo $db->getErrorMsg();
		}
		
		if($type=="update"){
			
			//update custom plugin id in updated profiles
			// retrieving vData custom plugin id
			$query->select($db->quoteName('extension_id'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element').' = '.$db->quote('custom'));
			$query->where($db->quoteName('folder').' = '.$db->quote('vdata'));
			$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
			$query->setLimit('1');
			$db->setQuery( $query );
			$pluginid = (int)$db->loadResult();
			$query->clear();
			
			// updating vData custom plugin id in vData profiles table
			$query->update($db->quoteName('#__vd_profiles'))->set('pluginid='.$pluginid);
			$db->setQuery( $query );
			$db->execute();
			$query->clear();
			echo $db->getErrorMsg();
			
		}
		
		// check weather cURL exists and enabled
		if(!function_exists('curl_version')){
			$msg = JText::_('Enable PHP CURL extension to get data from remote server.');
			echo JFactory::getApplication()->enqueueMessage($msg, 'warning');
		}
			
		
		$messages = (array)$this->messages;
	?>
	
	<style type="text/css">
	.adminform tr th{
		display:none;
	}

	/* TYPOGRAPHY AND SPACING */
	#vdata-installer td{
		font-size:11px;
		line-height:1.7;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	#vdata-installer td table td{
		padding:5px 2px 5px 10px;
	}

	/* MESSAGES */
	#vdata-message {
		border:1px solid #ccc;
		padding:13px;
		border-radius:2px;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vdata-message.error {
		border-color:#900;
		color: #900;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vdata-message.info {
		background:#ECEFF6;
		border-color:#c4cbdd;
		color:#555;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}

	#vdata-message.warning {
		border-color:#f90;
		color: #c30;
		font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	#stylized {
    background: none repeat scroll 0 0 #EBF4FB;
    border: 1px solid #B7DDF2;
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
	}
	.myform {
		height: auto;
		margin: 0 auto;
		padding: 14px;
		width: auto;
	}
	</style>
	<div id="stylized" class="myform">
	<table id="vdata-installer" width="100%" border="0" cellpadding="0" cellspacing="0">
		<?php
			foreach ($messages as $message) {
				?>
				<tr>
					<td><div id="vdata-message" class="<?php echo $message['type']; ?>"><?php echo ucfirst($message['type']) . ' : ' . $message['message']; ?></div></td>
				</tr>
				<?php
			}
		?>
		<tr>
			<td>
				<div><img src="../media/com_vdata/images/vdata-logo.png"  style="display: inline-block; height: 60px; vertical-align: middle;"/><h2 style="display: inline-block; margin: 0px 0px 0px 15px; font-size: 30px; vertical-align: middle; font-weight: normal; line-height: normal;">vData - Data Management Tool</h2></div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:700px; padding-left:10px;margin-top: 10px;">
					vData is Powerful, Secure, Intuitive and Easy-to-use Data Management Tool that will save your time, ensure accuracy and reduce your costs of Data Migration between systems by up to 90%. It also offers you the ability to generate Widgets to give insight about the database and to let you monitor and analyze database and server performance. It provides you a very simple and flexible way to Add / Update data to your Website in CSV, XML, JSON and Database format. <a href="<?php echo JRoute::_('index.php?option=com_vdata');?>">Explore Now</a>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<table>
					<tr>
						<td colspan="2">To get our latest news and promotions :</td>
					</tr>
					<tr>
						<td>Like us on Facebook :</td>
						<td>
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
							<div class="fb-like" data-href="https://www.facebook.com/wdmtechnologies" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
						</td>
					<tr>
						<td>Follow us on Twitter :</td>
						<td>
							<a href="https://twitter.com/wdmtechnologies" class="twitter-follow-button" data-show-count="false">Follow @wdmtechnologies</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</td>
					</tr>
                    <tr>
                    	<td colspan="2">Post on our <a href="http://www.wdmtech.com/support-forum" target="_blank">Support Forum</a> for any Assistance</td>
                    </tr>
					<tr>
						<td colspan="2">If you use vData, please post a rating and a review at <a href="http://extensions.joomla.org/extension/vdata" target="_blank">Joomla! Extension Directory</a>.</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
    </div>
	
	<?php 
	
    }
	
}
