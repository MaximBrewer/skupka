<?php
/**
 * Highslide JS Plugin
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

$app = JFactory::getApplication();
$app->registerEvent( 'onAfterDispatch', 'onAfterDispatchHighslide' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filecontent.file');
jimport( 'joomla.utilities.string');

function onAfterDispatchHighslide()
{
	$app      = JFactory::getApplication();
	$document = JFactory::getDocument();

	// check if site is active
	if (!($app->isSite() && is_a($document, 'JDocumentHTML'))) {
		return true;
	}

    $doc = JFactory::getDocument();
	$doc->addScript($base.'/media/plg_content_mavikthumbnails/highslide/highslide-with-gallery.packed.js');
        $doc->addStyleSheet($base.'/media/plg_content_mavikthumbnails/highslide/highslide.css');
        $doc->addScriptDeclaration('
           hs.graphicsDir = "'.$base.'/media/plg_content_mavikthumbnails/highslide/graphics/";
            hs.align = "center";
            hs.outlineType = "rounded-white";
            hs.numberPosition = "caption";
            hs.dimmingOpacity = 0.75;
			hs.marginBottom = 80;
            hs.showCredits = false;
            hs.transitions = ["expand", "crossfade"];
        ');

    $doc->addScriptDeclaration('
            hs.addSlideshow({
               
               interval: 3000,
               repeat: true,
               useControls: true,
               fixedControls: false,
               overlayOptions: {
                className: "large-dark",
                  opacity: 1,
                  position: "bottom center",
                  offsetX: 0,
                  offsetY: -10,
                  hideOnMouseOut: false
               },
               thumbstrip: {
                   position: "bottom center",
		   mode: "horizontal",
		   relativeTo: "viewport"
               }
            });
        ');
/*
	// get plugin info
	$plugin = JPluginHelper::getPlugin( 'content', 'highslide');
	if (count($plugin) == 0)
	{
		return true;
	}
	$params 	= new JRegistry;
	$params->loadString($plugin->params);

	// check whether plugin has been unpublished
	if (!$params->get('enabled', 1))
	{
		return true;
	}

	$headdata = $document->getHeadData();
	if ($headdata != null)
	{
		$keys= array_keys( $headdata['scripts']);
		$hs_base    = JURI::root(true). '/plugins/content/highslide/config/js/';
		foreach ($keys as $script )
		{
			$script = str_ireplace( $hs_base, "", $script );
			if ($script == "highslide-sitesettings.js"
				||JString::strpos( $script, "highslide-article-") === 0
				)
			{
				//	already have a config, get out
				return;
			}
		}
	}

	$needHighslide	=	($params->get('includehsconfig', 1) == 1);

	if (! $needHighslide)
	{
		$buffers = $document->GetBuffer();
		foreach( $buffers as $bufarray )
		{
			$buf = implode( '', $bufarray );
			if (preg_match( "/(class|rel)=\".*highslide/i", $buf, $match ))
			{
				$needHighslide = TRUE;
				break;
			}

		}
	}

	if ($needHighslide)
	{
		$dir = dirname( __FILE__ );
		$pos = JString::strrpos( $dir, "system" );
		if ($pos !== false)
		{
			$dir = JString::substr_replace( $dir, 'content', $pos, 6 );
		}
		require_once( $dir.DS.'highslide.php' );
		plgContentHighslide::_checkContent( $params, -1 );
	}
*/
	return true;
}

?>