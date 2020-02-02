<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaContent
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

namespace content\com_content\model\export;

defined('_JEXEC') or die;

/**
 * Export Joomla articles.
 *
 * @package     CSVI
 * @subpackage  JoomlaContent
 * @since       6.0
 */
class Content extends \CsviModelExports
{
	/**
	 * The custom fields that from other extensions.
	 *
	 * @var    array
	 * @since  6.5.0
	 */
	private $pluginfieldsExport = array();

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  7.2.0
	 */
	private $customFields = array();

	/**
	 * The Joomla content helper
	 *
	 * @var    \Com_ContentHelperCom_Content
	 * @since  6.0
	 */
	protected $helper;

	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @throws  \Exception
	 *
	 * @since   6.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Get some basic data
			require_once JPATH_SITE . '/components/com_content/helpers/route.php';
			$this->loadPluginFields();
			$this->loadCustomFields();

			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();
			$exportfields = $this->fields->getFields();

			// Group by fields
			$groupbyfields = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupby = array();

			if (isset($groupbyfields->name))
			{
				$groupbyfields = array_flip($groupbyfields->name);
			}
			else
			{
				$groupbyfields = array();
			}

			// Sort selected fields
			$sortfields = json_decode($this->template->get('sortfields', '', 'string'));
			$sortby = array();

			if (isset($sortfields->name))
			{
				$sortbyfields = array_flip($sortfields->name);
			}
			else
			{
				$sortbyfields = array();
			}

			$userfields[] = $this->db->quoteName('c.id');

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'category_path':
						$userfields[] = $this->db->quoteName('c.catid');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.catid');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.catid');
						}
						break;
					case 'article_url':
						$userfields[] = $this->db->quoteName('c.id');
						$userfields[] = $this->db->quoteName('cat.id', 'catid');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.id');
							$groupby[] = $this->db->quoteName('cat.id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.id');
							$sortby[] = $this->db->quoteName('cat.id');
						}
						break;
					case 'access':
					case 'alias':
					case 'asset_id':
					case 'checked_out':
					case 'checked_out_time':
					case 'created_by':
					case 'created_by_alias':
					case 'hits':
					case 'id':
					case 'language':
					case 'metadata':
					case 'metadesc':
					case 'metakey':
					case 'title':
					case 'version':
						$userfields[] = $this->db->quoteName('c.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.' . $field->field_name);
						}
					break;
					case 'show_title':
					case 'link_titles':
					case 'show_intro':
					case 'show_category':
					case 'link_category':
					case 'show_parent_category':
					case 'link_parent_category':
					case 'show_author':
					case 'link_author':
					case 'show_create_date':
					case 'show_modify_date':
					case 'show_publish_date':
					case 'show_item_navigation':
					case 'show_icons':
					case 'show_print_icon':
					case 'show_email_icon':
					case 'show_vote':
					case 'show_hits':
					case 'show_noauth':
					case 'urls_position':
					case 'alternative_readmore':
					case 'article_layout':
					case 'show_publishing_options':
					case 'show_article_options':
					case 'show_urls_images_backend':
					case 'show_urls_images_frontend':
						$userfields[] = $this->db->quoteName('c.id');
						$userfields[] = $this->db->quoteName('c.attribs');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.attribs');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.attribs');
						}
					break;
					case 'image_intro':
					case 'float_intro':
					case 'image_intro_alt':
					case 'image_intro_caption':
					case 'image_fulltext':
					case 'float_fulltext':
					case 'image_fulltext_alt':
					case 'image_fulltext_caption':
						$userfields[] = $this->db->quoteName('c.images');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.images');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.images');
						}
					break;
					case 'urla':
					case 'urlatext':
					case 'targeta':
					case 'urlb':
					case 'urlbtext':
					case 'targetb':
					case 'urlc':
					case 'urlctext':
					case 'targetc':
						$userfields[] = $this->db->quoteName('c.urls');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.urls');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.urls');
						}
						break;
					case 'tags':
						$userfields[] = $this->db->quoteName('c.id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('c.id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('c.id');
						}
						break;
					case 'custom':
						break;
					default:
						// Do not include custom fields into the query
						if (!in_array($field->field_name, $this->pluginfieldsExport)
							&& !in_array($field->field_name, $this->customFields))
						{
							$userfields[] = $this->db->quoteName($field->field_name);

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName($field->field_name);
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName($field->field_name);
							}
						}
						break;
				}
			}

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__content', 'c'));
			$query->leftJoin($this->db->quoteName('#__categories', 'cat') . ' ON ' . $this->db->quoteName('cat.id') . ' = ' . $this->db->quoteName('c.catid'));

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('c.state') . ' = ' . (int) $publish_state);
			}

			// Filter by language
			$language = $this->template->get('content_language');

			if ($language != '*')
			{
				$query->where($this->db->quoteName('c.language') . ' = ' . $this->db->quote($language));
			}

			// Filter by category
			$categories = $this->template->get('content_categories');

			if ($categories && $categories[0] != '*')
			{
				if ($this->template->get('incl_subcategory', false))
				{
					$subCategories = array();

					foreach ($categories as $categoryId)
					{
						$subCategories = $this->helper->getSubCategoryIds($categoryId);
					}

					if ($subCategories)
					{
						$categories = array_merge($subCategories, $categories);
					}
				}

				$query->where($this->db->quoteName('catid') . " IN ('" . implode("','", $categories) . "')");
			}

			$daterange      = $this->template->get('contentdaterange', '');
			$checkDatefield = $this->template->get('filterdatefield', 'created');

			if ($daterange)
			{
				$jdate       = \JFactory::getDate('now', 'UTC');
				$currentDate = $this->db->quote($jdate->format('Y-m-d'));

				switch ($daterange)
				{
					case 'lastrun':
						if (substr($this->template->getLastrun(), 0, 4) != '0000')
						{
							$query->where($this->db->quoteName('c.' . $checkDatefield) . ' > ' . $this->db->quote($this->template->getLastrun()));
						}
						break;
					case 'yesterday':
						$query->where(
							'DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') = DATE_SUB(' . $currentDate . ', INTERVAL 1 DAY)');
						break;
					case 'thisweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset    = $dayofweek - 1;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= ' . $currentDate);
						break;
					case 'lastweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset    = $dayofweek + 6;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofweek . ' DAY)');
						break;
					case 'thismonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= ' . $currentDate);
						break;
					case 'lastmonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$month      = date('n');
						$year       = date('y');

						if ($month > 1)
						{
							$month--;
						}
						else
						{
							$month = 12;
							$year--;
						}

						$daysinmonth = date('t', mktime(0, 0, 0, $month, 25, $year));
						$offset      = ($daysinmonth + $dayofmonth) - 1;

						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						break;
					case 'thisquarter':
						// Find out which quarter we are in
						$month   = $jdate->__get('month');
						$year    = date('Y');
						$quarter = ceil($month / 3);

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'lastquarter':
						// Find out which quarter we are in
						$month   = $jdate->__get('month');
						$year    = date('Y');
						$quarter = ceil($month / 3);

						if ($quarter == 1)
						{
							$quarter = 4;
							$year--;
						}
						else
						{
							$quarter--;
						}

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'thisyear':
						$year = date('Y');
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-01-01'));
						break;
					case 'lastyear':
						$year = date('Y');
						$year--;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('c.' . $checkDatefield) . ') < ' . $this->db->quote($year . '-01-01'));
						break;
				}
			}
			else
			{
				$fromDate = $this->template->get('fromdate', false);

				if ($fromDate)
				{
					$fdate = \JFactory::getDate($fromDate);
					$query->where($this->db->quoteName('c.' . $checkDatefield) . ' >= ' . $this->db->quote($fdate->toSql()));
				}

				$toDate = $this->template->get('todate', false);

				if ($toDate)
				{
					$tdate = \JFactory::getDate($toDate);
					$query->where($this->db->quoteName('c.' . $checkDatefield) . ' <= ' . $this->db->quote($tdate->toSql()));
				}
			}

			// Group the fields
			$groupby = array_unique($groupby);

			if (!empty($groupby))
			{
				$query->group($groupby);
			}

			// Sort set fields
			$sortby = array_unique($sortby);

			if (!empty($sortby))
			{
				$query->order($sortby);
			}

			// Add export limits
			$limits = $this->getExportLimit();

			// Execute the query
			$this->db->setQuery($query, $limits['offset'], $limits['limit']);
			$records = $this->db->getIterator();
			$this->log->add('Export query' . $query->__toString(), false);

			// Check if there are any records
			$logcount = $this->db->getNumRows();

			if ($logcount > 0)
			{
				foreach ($records as $record)
				{
					$this->log->incrementLinenumber();

					// Clean some settings
					$attribs = '';
					$images = '';
					$urls = '';

					foreach ($exportfields as $field)
					{
						$fieldname = $field->field_name;

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldvalue = $record->$fieldname;
						}
						else
						{
							$fieldvalue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'category_path':
								$query->clear()
									->select($this->db->quoteName('path'))
									->from($this->db->quoteName('#__categories'))
									->where($this->db->quoteName('id') . ' = ' . (int) $record->catid);
								$this->db->setQuery($query);
								$fieldvalue = $this->db->loadResult();
								break;
							case 'article_url':
								// Let's create a SEF URL
								$fieldvalue = $this->sef->getSefUrl(\ContentHelperRoute::getArticleRoute($record->id, $record->catid));
								break;
							case 'show_title':
							case 'link_titles':
							case 'show_intro':
							case 'show_category':
							case 'link_category':
							case 'show_parent_category':
							case 'link_parent_category':
							case 'show_author':
							case 'link_author':
							case 'show_create_date':
							case 'show_modify_date':
							case 'show_publish_date':
							case 'show_item_navigation':
							case 'show_icons':
							case 'show_print_icon':
							case 'show_email_icon':
							case 'show_vote':
							case 'show_hits':
							case 'show_noauth':
							case 'urls_position':
							case 'alternative_readmore':
							case 'article_layout':
							case 'show_publishing_options':
							case 'show_article_options':
							case 'show_urls_images_backend':
							case 'show_urls_images_frontend':
								if (empty($attribs))
								{
									$attribs = json_decode($record->attribs);
								}

								if (isset($attribs->$fieldname))
								{
									$fieldvalue = $attribs->$fieldname;
								}
								break;
							case 'image_intro':
							case 'float_intro':
							case 'image_intro_alt':
							case 'image_intro_caption':
							case 'image_fulltext':
							case 'float_fulltext':
							case 'image_fulltext_alt':
							case 'image_fulltext_caption':
								if (empty($images))
								{
									$images = json_decode($record->images);
								}

								if (isset($images->$fieldname))
								{
									$fieldvalue = $images->$fieldname;
								}
								break;
							case 'urla':
							case 'urlatext':
							case 'targeta':
							case 'urlb':
							case 'urlbtext':
							case 'targetb':
							case 'urlc':
							case 'urlctext':
							case 'targetc':
								if (empty($urls))
								{
									$urls = json_decode($record->urls);
								}

								if (isset($urls->$fieldname))
								{
									$fieldvalue = $urls->$fieldname;
								}
								break;
							case 'tags':
								$query->clear()
									->select($this->db->quoteName('tag_id'))
									->from($this->db->quoteName('#__contentitem_tag_map'))
									->where($this->db->quoteName('content_item_id') . ' = ' . (int) $record->id)
									->where($this->db->quoteName('type_alias') . ' = ' . $this->db->quote('com_content.article'));
								$this->db->setQuery($query);
								$tagIds = $this->db->loadObjectList();

								$tags = array();

								if ($tagIds)
								{
									foreach ($tagIds as $tagId)
									{
										$query->clear()
											->select($this->db->quoteName('path'))
											->from($this->db->quoteName('#__tags'))
											->where($this->db->quoteName('id') . ' = ' . (int) $tagId->tag_id);
										$this->db->setQuery($query);
										$tags[] = $this->db->loadResult();
									}
								}

								$fieldvalue = implode('|', $tags);

								break;
							default:
								if (in_array($fieldname, $this->pluginfieldsExport))
								{
									$fieldvalue = '';

									// Get value from content plugin
									$dispatcher = new \RantaiPluginDispatcher;
									$dispatcher->importPlugins('csviext', $this->db);
									$result = $dispatcher->trigger(
										'onExportContent',
										array(
											'extension' => 'joomla',
											'operation' => 'content',
											'id' => $record->id,
											'fieldname' => $fieldname,
											'log' => $this->log
										)
									);

									if (isset($result[0]))
									{
										$fieldvalue = $result[0];
									}
								}

								if (in_array($fieldname, $this->customFields))
								{
									$query->clear()
										->select($this->db->quoteName('id'))
										->from($this->db->quoteName('#__fields'))
										->where($this->db->quoteName('name') . '  = ' . $this->db->quote($fieldname));
									$this->db->setQuery($query);
									$fieldId = $this->db->loadResult();
									$itemId = $record->id;

									$query->clear()
										->select($this->db->quoteName('value'))
										->from($this->db->quoteName('#__fields_values'))
										->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
										->where($this->db->quoteName('item_id') . ' = ' . (int) $itemId);
									$this->db->setQuery($query);
									$fieldResult = $this->db->loadObjectList();

									// Check if the custom field is a multiple image list
									if ($this->fields->checkCustomFieldType($fieldname, 'imagelist'))
									{
										$fieldArray = array();

										foreach ($fieldResult as $result)
										{
											$fieldArray[] = $result->value;
										}

										$fieldvalue = implode('|', $fieldArray);
									}
									else
									{
										if (!empty($fieldResult))
										{
											$fieldvalues = array();

											foreach ($fieldResult as $item)
											{
												$fieldvalues[] = $item->value;
											}

											$fieldvalue = implode('|', $fieldvalues);
										}
									}

									if ($fieldvalue && $this->fields->checkCustomFieldType($fieldname, 'calendar'))
									{
										$fieldvalue = $this->fields->getDateFormat($fieldname, $fieldvalue, $field->column_header);
									}
								}

								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldvalue);
					}

					// Output the data
					$this->addExportFields();

					// Output the contents
					$this->writeOutput();
				}
			}
			else
			{
				$this->addExportContent(\JText::_('COM_CSVI_NO_DATA_FOUND'));

				// Output the contents
				$this->writeOutput();
			}
		}
	}

	/**
	 * Get a list of plugin fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	private function loadPluginFields()
	{
		$dispatcher = new \RantaiPluginDispatcher;
		$dispatcher->importPlugins('csviext', $this->db);
		$result = $dispatcher->trigger(
			'getAttributes',
			array(
				'extension' => 'joomla',
				'operation' => 'content',
				'log' => $this->log
			)
		);

		if (is_array($result) && !empty($result))
		{
			$this->pluginfieldsExport = array_merge($this->pluginfieldsExport, $result[0]);
		}
	}

	/**
	 * Get a list of custom fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   7.2.0
	 *
	 * @throws  \Exception
	 */
	private function loadCustomFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('name'))
			->from($this->db->quoteName('#__fields'))
			->where($this->db->quoteName('state') . ' = 1')
			->where($this->db->quoteName('context') . ' = ' . $this->db->quote('com_content.article'));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $result)
		{
			$this->customFields[] = $result->name;
		}

		$this->log->add('Load the Joomla custom fields for articles');
	}
}
