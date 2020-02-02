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

if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

if (file_exists(JPATH_ROOT . '/plugins/system/sh404sef/sh404sef.php')) :

	?>
	<div style="text-align: justify;" xmlns="http://www.w3.org/1999/html">
		<h1>sh404SEF installed succesfully! Please read the following</h1>

		<p>
			This component
		<ul>
			<li>rewrites Joomla! URLs to be Search Engine Friendly.</li>
			<li>performs various SEO improvements</li>
			<li>adds security features</li>
			<li>insert Google analytics snippet and create analytics reports in its control panel</li>
		</ul>
		</p>

		<p>
			If it is the first time you use sh404SEF, it has been installed but most features are
			<strong>disabled</strong> right now.
			You must first use sh404SEF control panel (from the <a href="index.php?option=com_sh404sef">sh404SEF
				Components</a> menu item of Joomla backend),
			<strong>enable whichever part you want to use and save</strong> before it will become active.
			Before you do so, please read the next paragraphs which have important information for you. If you are
			upgrading from a previous version of sh404SEF,
			then all your settings have been preserved, the component is activated and you can start browsing your site
			frontpage right away.
		</p>

		<h2>URL Rewriting</h2>

		<p>If you decided to use URL rewriting on your site, you must setup a .htaccess file (for the Apache web server)
			or equivalent configuration for other web servers. If your server is not configured correctly, you will be
			able to load the home page of your site,but all other pages will generate a 404 - Page not found error.</p>

		<p>This is a requirement of your web server, and there's nothing Joomla or sh404SEF can do about it.</p>

		<p>Joomla comes with the most generic .htaccess file. It will probably work right away on your system, or may
			need
			adjustments. The Joomla supplied file is called htaccess.txt, is located in the root directory of your site,
			and must be
			renamed into .htaccess before it will have any effect. You will find additional information about .htaccess
			at
			<a target="_blank" href="https://weeblr.com/documentation">the documentation page</a>.</p>

		<h2>Extensions</h2>

		<p>sh404SEF can build SEF URL for many Joomla components.
			It does it through a <strong>"plugin" system</strong>, and comes with a dedicated plugin for each of Joomla
			standard components (Contact, Weblinks, Newsfeed, Content of course,...).
			It also comes with native plugins for common components such as Community Builder, JomSocial, Kunena or
			Virtuemart.</p>

		<p>sh404SEF can also automatically make use of plugins designed for Joomla's own format, router.php files.
			Such plugins are often delivered and installed automatically when you install a component.
			Please note that when using these "foreign" plugins, you may experience compatibility issues.
		</p>

		<p>
			However, Joomla having several hundreds extensions available, not all of them have a plugin to tell sh404SEF
			how its URL should be built.
			When it does not have a plugin for a given component, sh404SEF will switch back to Joomla 1.0.x standard SEF
			URL, similar to mysite.com/component/option,com_sample/task,view/id,23/Itemid,45/.
			This is normal, and can't be otherwise unless someone writes a plugin for this component (your assistance
			in doing so is very much welcomed!)
		</p>

		<h2>Documentation</h2>

		<p>You will find detailed documentation on our <a target="_blank" href="https://weeblr.com/documentation">website</a>,
			including a <strong>Getting Started</strong> video/
		</p>

		<p></p>

		<p></p>
	</div>

	<?php

else :

	?>

	<h1>Sorry, something went wrong while installing sh404SEF on your web site.</h1>
	<p>
		Please try uninstalling first, then check permissions on your file system, and make sure Joomla can write to the
		/plugin directory.
		Or contact your site administrator for assistance.
	</p>
	<p>You can also report this on our website at <a target="_blank" href="https://weeblr.com/helpdesk">our support
			helpdesk.</a>
	</p>

	<p></p>
	<?php

endif;

