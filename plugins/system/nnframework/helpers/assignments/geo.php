<?php
/**
 * NoNumber Framework Helper File: Assignments: Geo
 *
 * @package         NoNumber Framework
 * @version         15.9.5525
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JLoader::registerNamespace('GeoIp2', JPATH_LIBRARIES . '/geoip');
JLoader::registerNamespace('MaxMind', JPATH_LIBRARIES . '/geoip');

use GeoIp2\GeoIp;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignment.php';

class NNFrameworkAssignmentsGeo extends NNFrameworkAssignment
{
	var $geo = null;

	/**
	 * passContinents
	 */
	function passContinents()
	{
		if (!$this->getGeo() || empty($this->geo->continentCode))
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->geo->continentCode);
	}

	/**
	 * passCountries
	 */
	function passCountries()
	{
		if (!$this->getGeo() || empty($this->geo->countryCode))
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->geo->countryCode);
	}

	/**
	 * passRegions
	 */
	function passRegions()
	{
		if (!$this->getGeo() || empty($this->geo->countryCode) || empty($this->geo->regionCode))
		{
			return $this->pass(false);
		}

		$region = $this->geo->countryCode . '-' . $this->geo->regionCode;

		return $this->passSimple($region);
	}

	/**
	 * passPostalcodes
	 */
	function passPostalcodes()
	{
		if (!$this->getGeo() || empty($this->geo->postalCode))
		{
			return $this->pass(false);
		}

		// replace dashes with dots: 730-0011 => 730.0011
		$postalcode = str_replace('-', '.', $this->geo->postalCode);

		return $this->passInRange($postalcode);
	}

	public function getGeo($ip = '')
	{
		if ($this->geo !== null)
		{
			return $this->geo;
		}

		if (!class_exists('GeoIp2\\GeoIp'))
		{
			return null;
		}

		$geo = new GeoIp($ip);

		$this->geo = $geo->get();

		return $this->geo;
	}
}
