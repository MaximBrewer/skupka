<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

?>

<!-- start analytics panel markup -->
<div class="sh404sef-analytics wbl-theme-default">
	<?php

	if ($this->isAjaxTemplate) :
		?>

		<div class="row-fluid">

			<div id="shl-sidebar-container" class="span3">
				<?php echo $this->sidebar; ?>
				<div class="row"></div>
				<div id="analyticscontent_headers"></div>
			</div>

			<div class="span9">
				<?php

				if (!empty($this->message))
				{
					echo ShlHtmlBs_Helper::alert($this->message, $type = 'info', $dismiss = true);
				}

				echo $this->loadTemplate($this->joomlaVersionPrefix . '_' . $this->options['report']);

				?>
			</div>
		</div>
		<div class="sh404sef-footer-container">
			<?php echo $this->footerText; ?>
		</div>
		<?php
	else :
		// this is one of the ajax calls to fetch one of the bits making up the reports
		// headers, global, visits, perf, top5referrers, top5urls
		// if there was an error while fetching data (due to credentials not set for instance)
		// we don't display anything, except for the 'headers' request, which is
		// precisely the template where the 'error' or information message
		// will be displayed
		if (!empty($this->analytics->status) || $this->options['subrequest'] == 'headers')
		{
			echo $this->loadTemplate($this->joomlaVersionPrefix . '_' . $this->options['subrequest']);
		}
		else
		{
			$msg = empty($this->analytics->statusMessage) ? 'No data available, please complete configuration.' : JText::_('COM_SH404SEF_ANALYTICS_AUTH_ERROR') . $this->analytics->statusMessage;
			echo $msg;
		}
	endif;

	?>
</div>
<!-- end analytics panel markup -->

