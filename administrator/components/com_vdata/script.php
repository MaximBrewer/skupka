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
 * Script file of vData component
 */
class com_vdataInstallerScript
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
		
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
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
