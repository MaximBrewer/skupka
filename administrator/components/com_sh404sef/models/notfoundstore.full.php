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

/**
 * Stores 404 infos into database
 */
class Sh404sefModelNotfoundstore
{

	private static $_instance = null;

	/**
	 * Singleton method
	 *
	 * @param string $extension
	 *            extension name, with com_ - ie com_content
	 * @return object instance of Sh404sefModelCategories
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new Sh404sefModelNotfoundstore();
		}

		return self::$_instance;
	}

	public function store($reqPath, $config)
	{
		// normalize
		$reqPath = JString::ltrim($reqPath, '/');

		// optionnally log the 404 details
		if ($config->shLog404Errors && !empty($reqPath))
		{
			try
			{
				$rawResults = ShlDbHelper::selectAssocList('#__sh404sef_urls', array('oldurl', 'newurl', 'dateadd', 'id', 'cpt', 'rank'), array('oldurl' => $reqPath), array(), $orderBy = array('rank'));

				// do we have at least one 404 existing records?
				if (!empty($rawResults))
				{
					$invalidRecordsIds = array();
					$notFoundRecord = null;
					foreach ($rawResults as $rawResult)
					{
						if (empty($rawResult['newurl']) && !empty($rawResult['dateadd']) && empty($notFoundRecord))
						{
							// first valid 404 record, use that
							$notFoundRecord = $rawResult;
						}
						else
						{
							// this is not a valid 404 record, or we already have such 404 record
							// let's decide what to do with it
							// if a 404, delete it (only one record per URL)
							if (empty($rawResult['newurl']))
							{
								$invalidRecordsIds[] = $rawResult['id'];
							}
							// if not a 404 and not a custom URL, delete also
							if (!empty($rawResult['newurl']) && $rawResult['dateadd'] == '0000-00-00')
							{
								$invalidRecordsIds[] = $rawResult['id'];
							}
						}
					}

					// do we have invalid records, ie either multiple 404 records for same SEf,
					// or non-404 records for that SEF - but excluding custom URLs, which we want
					// to keep.
					if (!empty($invalidRecordsIds))
					{
						// delete those invalid records
						ShlDbHelper::deleteIn('#__sh404sef_urls', 'id', $invalidRecordsIds, ShlDbHelper::INTEGER);

						// they might be in the disk cache file as well
						// though that would be a bug
						Sh404sefHelperCache::purge();
					}
				}

				$updatedRecord = empty($notFoundRecord) ? array(
					'id' => 0,
					'cpt' => 0,
					'rank' => 0,
					'oldurl' => $reqPath,
					'newurl' => '',
					'option' => '',
					'dateadd' => ShlSystem_Date::getUTCNow('Y-m-d'),
					'referrer_type' => Sh404sefHelperUrl::IS_UNKNOWN, // allow displaying a warning in 404 list, w/o having to do a join
				) : $notFoundRecord;

				// update counter
				$updatedRecord['cpt'] += 1;

				// find if internal request
				$referrer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
				$isInternal = !empty($referrer) && JUri::isInternal($referrer);
				$isInternal ? Sh404sefHelperUrl::IS_INTERNAL : Sh404sefHelperUrl::IS_EXTERNAL;

				// get referrer and find if this is a local URL (but don't override a previous INTERNAL FLAG on that URL)
				if (empty($updatedRecord['referrer_type'])
					|| (!empty($updatedRecord['referrer_type']) && $updatedRecord['referrer_type'] != Sh404sefHelperUrl::IS_INTERNAL)
				)
				{
					$updatedRecord['referrer_type'] = $isInternal;
				}

				// write back the record, with updated counter
				if (empty($notFoundRecord))
				{
					ShlDbHelper::insert('#__sh404sef_urls', $updatedRecord);
				}
				else
				{
					ShlDbHelper::update('#__sh404sef_urls', $updatedRecord, array('id' => $updatedRecord['id']));
				}

				// record a detailed log of the 404, if set to
				if ($config->log404sHits)
				{
					$recorder = Sh404sefModelReqrecorder::getInstance(Sh404sefModelReqrecorder::REQUEST_404);
					$recorder->record($reqPath, '', $isInternal, $referrer);
				}
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, ' Database error: ' . $e->getMessage());
				return false;
			}
		}

		return true;
	}
}
