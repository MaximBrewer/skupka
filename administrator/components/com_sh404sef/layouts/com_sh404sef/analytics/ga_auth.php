<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

?>

<script type="text/javascript">
/** enable Ga Auth */
var gaAuthRequest = {"targetUrl": 'https://accounts.google.com/o/oauth2/auth'
	+ '?scope=https://www.googleapis.com/auth/analytics'
	+ '&redirect_uri=urn:ietf:wg:oauth:2.0:oob'
	+ '&response_type=code'
	+ '&client_id=<?php echo $displayData['clientId']['client_id_def']['id']; ?>'
};
sh404sefApp.gaAuth.add(gaAuthRequest);
</script>

<div class="wbga_container">
	<?php if (empty($displayData['authRequired'])) : ?>
		<span class="wbga_auth_good"><?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_AUTHORIZED'); ?></span>
		<button type="button" class="wbga_clearauthbutton" title="<?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_CLEAR_AUTH_DESC'); ?>"><?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_CLEAR_AUTH'); ?></button>
	<?php endif; ?>
	<button type="button" class="wbga_authbutton"
	        title="<?php echo empty($displayData['authRequired']) ? JText::_('COM_SH404SEF_ANALYTICS_AUTH_RENEW_AUTH_DESC') : JText::_('COM_SH404SEF_ANALYTICS_AUTH_REQUIRED_DESC'); ?>">
		<?php echo empty($displayData['authRequired']) ? JText::_('COM_SH404SEF_ANALYTICS_AUTH_RENEW_AUTH') : JText::_('COM_SH404SEF_ANALYTICS_AUTH_REQUIRED'); ?></button>
	<span class="wbga_authinputhint wbga_warning wbga_close"><?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_INPUT_HINT'); ?></span>
	<span class="wbga_authclearhint wbga_warning wbga_close"><?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_CLEAR_HINT'); ?></span>
	<input type="text" disabled="disabled" class="wbga_authinput wbga_close" name="jform[wbgaauth_auth_token]"
	       id="jform_wbgaauth_auth_token" value="" size="50" max-length="50"
	       placeholder="<?php echo JText::_('COM_SH404SEF_ANALYTICS_AUTH_INPUT_PLACEHOLDER'); ?>">
	<input type="hidden" value="0" name="jform[wbga_clearauthorization]" id="jform_wbga_clearauthorization">
</div>
