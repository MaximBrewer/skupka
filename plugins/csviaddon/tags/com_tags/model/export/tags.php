<?php
/**
 * @package     CSVI
 * @subpackage  Tags
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

namespace tags\com_tags\model\export;

defined('_JEXEC') or die;

/**
 * Export Joomla tags.
 *
 * @package     CSVI
 * @subpackage  Joomla tags
 * @since       7.7.0
 */
class Tags extends \CsviModelExports
{
	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Build something fancy to only get the fieldnames the user wants
			$exportFields = $this->fields->getFields();

			// Group by fields
			$groupFields = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupBy     = array();

			if (isset($groupFields->name))
			{
				$groupByFields = array_flip($groupFields->name);
			}
			else
			{
				$groupByFields = array();
			}

			// Sort selected fields
			$sortFields = json_decode($this->template->get('sortfields', '', 'string'));
			$sortBy     = array();

			if (isset($sortFields->name))
			{
				$sortByFields = array_flip($sortFields->name);
			}
			else
			{
				$sortByFields = array();
			}

			// Fields which are needed for getting contents
			$userFields = array();

			foreach ($exportFields as $field)
			{
				switch ($field->field_name)
				{
					case 'id':
					case 'alias':
					case 'parent_id':
					case 'lft':
					case 'rgt':
					case 'level':
					case 'path':
					case 'title':
					case 'note':
					case 'description':
					case 'published':
					case 'checked_out':
					case 'checked_out_time':
					case 'access':
					case 'params':
					case 'metadesc':
					case 'metakey':
					case 'metadata':
					case 'created_user_id':
					case 'created_time':
					case 'created_by_alias':
					case 'modified_user_id':
					case 'modified_time':
					case 'images':
					case 'urls':
					case 'hits':
					case 'language':
					case 'version':
					case 'publish_up':
					case 'publish_down':
						$userFields[] = $this->db->quoteName('tags.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('tags.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('tags.' . $field->field_name);
						}
						break;
					case 'tag_layout':
					case 'tag_link_class':
						$userFields[] = $this->db->quoteName('tags.params');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('tags.params');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('tags.params');
						}
						break;
					case 'author':
					case 'robots':
						$userFields[] = $this->db->quoteName('tags.metadata');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('tags.metadata');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('tags.metadata');
						}
						break;
					case 'parent_tag_alias':
						$userFields[] = $this->db->quoteName('tags.parent_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('tags.parent_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('tags.parent_id');
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
						$userFields[] = $this->db->quoteName('tags.images');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('tags.images');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('tags.images');
						}
						break;
					case 'custom':
						break;
					default:
						$userFields[] = $this->db->quoteName($field->field_name);

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName($field->field_name);
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName($field->field_name);
						}

						break;
				}
			}

			// Build the query
			$userFields = array_unique($userFields);
			$query      = $this->db->getQuery(true);
			$query->select(implode(",\n", $userFields));
			$query->from($this->db->quoteName('#__tags', 'tags'));

			// Make sure the Parent ID is always greater than 0 as we don't want to export the root
			$query->where('parent_id > 0');

			// Group the fields
			$groupBy = array_unique($groupBy);

			if (0 !== count($groupBy))
			{
				$query->group($groupBy);
			}

			// Sort set fields
			$sortBy = array_unique($sortBy);

			if (0 !== count($sortBy))
			{
				$query->order($sortBy);
			}

			// Add export limits
			$limits = $this->getExportLimit();

			// Execute the query
			$this->db->setQuery($query, $limits['offset'], $limits['limit']);
			$records = $this->db->getIterator();
			$this->log->add('Export query' . $query->__toString(), false);

			// Check if there are any records
			$logCount = $this->db->getNumRows();

			if ($logCount > 0)
			{
				foreach ($records as $record)
				{
					$this->log->incrementLinenumber();

					// Clean some settings
					$params   = '';
					$image    = '';
					$metadata = '';

					foreach ($exportFields as $field)
					{
						$fieldName  = $field->field_name;
						$fieldValue = '';

						// Set the field value
						if (isset($record->$fieldName))
						{
							$fieldValue = $record->$fieldName;
						}

						// Process the field
						switch ($fieldName)
						{
							case 'tag_layout':
							case 'tag_link_class':
								if (empty($params))
								{
									$params = json_decode($record->params);
								}

								if (isset($params->$fieldName))
								{
									$fieldValue = $params->$fieldName;
								}
								break;
							case 'author':
							case 'robots':
								if (empty($metadata))
								{
									$metadata = json_decode($record->metadata);
								}

								if (isset($metadata->$fieldName))
								{
									$fieldValue = $metadata->$fieldName;
								}
								break;
							case 'parent_tag_alias':
								$query->clear()
									->select($this->db->quoteName('alias'))
									->from($this->db->quoteName('#__tags'))
									->where($this->db->quoteName('id') . ' = ' . (int) $record->parent_id);
								$this->db->setQuery($query);
								$fieldValue = $this->db->loadResult();
								break;
							case 'image_intro':
							case 'float_intro':
							case 'image_intro_alt':
							case 'image_intro_caption':
							case 'image_fulltext':
							case 'float_fulltext':
							case 'image_fulltext_alt':
							case 'image_fulltext_caption':
								if (empty($image))
								{
									$image = json_decode($record->metadata);
								}

								if (isset($image->$fieldName))
								{
									$fieldValue = $image->$fieldName;
								}
								break;
							default:
								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldValue);
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
}
