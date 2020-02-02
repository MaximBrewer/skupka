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
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefModelSefurls extends Sh404sefClassBasemodel
{

	public function getSefURLFromCacheOrDB($nonSefUrl, &$sefUrl)
	{

		$sefConfig = Sh404sefFactory::getConfig();
		if (empty($nonSefUrl))
		{
			return sh404SEF_URLTYPE_NONE;
		}

		$sefUrl = '';
		$urlType = sh404SEF_URLTYPE_NONE;
		if ($sefConfig->shUseURLCache)
		{
			$urlType = Sh404sefHelperCache::getSefUrlFromCache($nonSefUrl, $sefUrl);
		}
		// Check if the url is already saved in the database.
		if ($urlType == sh404SEF_URLTYPE_NONE)
		{
			$urlType = $this->getSefUrlFromDatabase($nonSefUrl, $sefUrl);
			if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404)
			{
				return $urlType;
			}
			else
			{
				if ($sefConfig->shUseURLCache)
				{
					Sh404sefHelperCache::addSefUrlToCache($nonSefUrl, $sefUrl, $urlType);
				}
			}
		}
		return $urlType;
	}

	public function getSefUrlFromDatabase($nonSefUrl, &$sefUrl)
	{
		try
		{
			$result = ShlDbHelper::selectObject($this->_getTableName(), array('oldurl', 'dateadd'), array('newurl' => $nonSefUrl));
		}
		catch (Exception $e)
		{
			return sh404SEF_URLTYPE_NONE;
		}

		// if match is empty, well, this should not happen
		if (empty($result->oldurl))
		{
			return sh404SEF_URLTYPE_NONE;
		}

		// store SEF url match found for non-sef
		$sefUrl = $result->oldurl;

		return $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
	}

	/**
	 * Fetch a non-sef url directly from database
	 *
	 * @param string $sefUrl the sefurl we are searching a non sef for
	 * @param string $nonSefUrl will be set to non sef url found
	 * @return integer code, either none found, or the url pair type: custom or automatic
	 */
	public function getNonSefUrlFromDatabase(& $sefUrl, & $nonSefUrl)
	{
		$record = $this->getNonSefUrlRecordFromDatabase($sefUrl, $nonSefUrl);
		return $record['status'];
	}

	/**
	 * Fetch a non-sef url directly from database
	 *
	 * @param string $sefUrl the sefurl we are searching a non sef for
	 * @param string $nonSefUrl will be set to non sef url found
	 * @return array('url','status'): status=either none found, or the url pair type: custom or automatic, url=the url record or null
	 */
	public function getNonSefUrlRecordFromDatabase(& $sefUrl, & $nonSefUrl)
	{
		try
		{
			$rawResults = ShlDbHelper::selectObjectList($this->_getTableName(), array('oldurl', 'newurl', 'dateadd', 'id', 'cpt', 'rank'), array('oldurl' => $sefUrl), array(), $orderBy = array('rank'));
		}
		catch (Exception $e)
		{
			return array('status' => sh404SEF_URLTYPE_NONE, 'url' => null);
		}

		if (empty($rawResults))
		{
			// no match, that's a 404 for us
			return array('status' => sh404SEF_URLTYPE_404, 'url' => null);
		}

		// clean up results: due to a bug, in versions 4.6.0 and 4.7.0
		// we would create 404 records even if the same SEF also had records with associated non-SEF
		// this would happen if the URL was created first, then deleted/unpublished
		// we must delete those invalid records
		$validatedResults = array();
		$notFoundResults = array();
		foreach ($rawResults as $rawResult)
		{
			if (!empty($rawResult->newurl))
			{
				$validatedResults[] = $rawResult;
			}
			else
			{
				$notFoundResults[] = $rawResult->id;
			}
		}

		// do we have valid SEF *AND* 404s? this can happen if an article is
		// unpublished for instance, then republished
		if (!empty($validatedResults) && !empty($notFoundResults))
		{
			// delete those invalid 404s
			ShlDbHelper::deleteIn($this->_getTableName(), 'id', $notFoundResults, ShlDbHelper::INTEGER);

			// they might be in the disk cache as well!
			Sh404sefHelperCache::purge();
		}
		// end of cleanup

		// we have at leasst one valid result
		$result = array_shift($validatedResults);

		if (empty($result->newurl))
		{
			// no match, that's a 404 for us
			return array('status' => sh404SEF_URLTYPE_404, 'url' => null);
		}

		// found it
		$nonSefUrl = $result->newurl;
		// also adjust sefurl, as the one we have found in db might have a different case from original
		$sefUrl = $result->oldurl;

		// return code and record found: either custom or automatic url
		return array('status' => $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM, 'url' => $result);
	}

	protected function _getTableName()
	{
		return '#__sh404sef_urls';
	}

}
