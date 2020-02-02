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

namespace content\com_content\model\import;

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Content import.
 *
 * @package     CSVI
 * @subpackage  JoomlaContent
 * @since       6.0
 */
class Content extends \RantaiImportEngine
{
	/**
	 * Content table.
	 *
	 * @var    \ContentTableContent
	 * @since  6.0
	 */
	private $content = null;

	/**
	 * The Joomla content helper
	 *
	 * @var    \Com_ContentHelperCom_Content
	 * @since  6.0
	 */
	protected $helper = null;

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  7.2.0
	 */
	private $customFields = '';

	/**
	 * Run this before we start.
	 *
	 * @return  void.
	 *
	 * @throws  \Exception
	 *
	 * @since   7.2.0
	 */
	public function onBeforeStart()
	{
		// Load the tables that will contain the data
		$this->loadCustomFields();
	}

	/**
	 * Start the product import process.
	 *
	 * @return  bool  True on success | false on failure.
	 *
	 * @since   6.0
	 */
	public function getStart()
	{
		// Process data
		foreach ($this->fields->getData() as $fields)
		{
			foreach ($fields as $name => $details)
			{
				$value = $details->value;

				switch ($name)
				{
					case 'category_path':
						$this->setState('catid', $this->helper->getCategoryId($value));
						$this->setState($name, $value);
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		$requiredFields = true;

		// Bind the data and set the title
		$this->setState('title', $this->getState('title', $this->getState('alias', false)));
		$this->content->bind(ArrayHelper::fromObject($this->state));

		if ($this->content->check())
		{
			$this->setState('alias', $this->content->get('alias'));

			// There must be an id or alias and catid or category_path
			if ($this->getState('id', false) || $this->getState('title', false)
				|| ($this->getState('alias', false) && ($this->getState('catid', false) || $this->getState('category_path', false))))
			{
				$this->loaded = true;

				if (!$this->getState('id', false))
				{
					$this->setState('id', $this->helper->getContentId($this->getState('alias', false), $this->getState('catid', false)));
				}

				// Load the current content data
				if ($this->content->load($this->getState('id', 0)))
				{
					if (!$this->template->get('overwrite_existing_data'))
					{
						$this->log->add('Article ' . $this->getState('alias') . 'not updated because the option overwrite existing data is set to No');
						$this->loaded = false;
					}
				}

				if (!$this->getState('id') && trim($this->getState('introtext')) === '' && trim($this->getState('fulltext')) === '')
				{
					$this->log->addStats('information', \JText::_('COM_CSVI_ARTICLE_HAS_NO_TEXT'));
				}
			}
			else
			{
				$requiredFields = false;
			}
		}
		else
		{
			$requiredFields = false;
		}

		if (!$requiredFields)
		{
			$field        = array('id or alias or title', 'catid or category_path');
			$this->loaded = false;
			$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_NO_REQUIRED_FIELD_FOUND', implode(',', $field)));
		}

		return true;
	}

	/**
	 * Process a record.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no product SKU or product ID can be found.
	 *
	 * @since   6.0
	 */
	public function getProcessRecord()
	{
		if ($this->loaded)
		{
			if (!$this->getState('id', false) && $this->template->get('ignore_non_exist'))
			{
				// Do nothing for new products when user chooses to ignore new products
				$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->getState('alias', '')));
			}
			else
			{
				// Set the attributes
				$this->setAttributes();

				// Set the images
				$this->setImages();

				// Set the urls
				$this->setUrls();

				// Check for meta data
				$this->setMetadata();

				// Data must be in an array
				$data = ArrayHelper::fromObject($this->state);

				// Add a creating date if there is no product_id
				if (!$this->getState('id', false))
				{
					if (!$this->getState('created_by'))
					{
						$this->content->created_by = $this->userId;
					}

					if (!$this->getState('created'))
					{
						$this->content->created = $this->date->toSql();
					}
				}
				else
				{
					if (!$this->getState('modified', false))
					{
						$this->content->modified = $this->date->toSql();
					}

					if (!$this->getState('modified_by'))
					{
						$this->content->modified_by = $this->userId;
					}
				}

				$this->content->bind($data);

				// Check if we use a given order id
				if ($this->template->get('keepid'))
				{
					$this->content->checkId();
				}

				try
				{
					$this->content->store();

					if (!$this->getState('id', 0))
					{
						$this->log->addStats('Added', \JText::_('COM_CSVI_JOOMLA_CONTENT_ADDED'));
					}
					else
					{
						$this->log->addStats('Updated', \JText::_('COM_CSVI_JOOMLA_CONTENT_UPDATED'));
					}

					$this->processCustomFields($this->content->id);
					$this->processTags($this->content->id);

					// Check if there is associations to import
					if ($this->getState('associations', false))
					{
						$this->processAssociations($this->content->id);
					}
				}
				catch (Exception $e)
				{
					$this->log->add('Cannot add Joomla content. Error: ' . $e->getMessage(), false);
					$this->log->addStats('incorrect', $e->getMessage());

					return false;
				}

				return true;
			}
		}

		return false;
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
		$this->customFields = $this->db->loadObjectList();

		$this->log->add('Load the Joomla custom fields for articles');
	}

	/**
	 * Update custom fields data.
	 *
	 * @param   int  $id  Id of the article
	 *
	 * @return  bool Returns true if all is OK | Returns false otherwise
	 *
	 * @since   7.2.0
	 */
	private function processCustomFields($id)
	{
		if (count($this->customFields) === 0)
		{
			$this->log->add('No custom fields found', false);

			return false;
		}

		foreach ($this->customFields as $field)
		{
			$fieldName = $field->name;

			if ($this->getState($fieldName, ''))
			{
				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('id'))
					->from($this->db->quoteName('#__fields'))
					->where($this->db->quoteName('name') . '  = ' . $this->db->quote($fieldName));
				$this->db->setQuery($query);
				$fieldId = $this->db->loadResult();

				$query->clear()
					->select(
						$this->db->quoteName(
							array(
								'field_id',
								'category_id'
							)
						)
					)
					->from($this->db->quoteName('#__fields_categories'))
					->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
					->where($this->db->quoteName('category_id') . ' = ' . (int) $this->getState('catid', false));
				$this->db->setQuery($query);
				$result = $this->db->loadResult();

				if (!$result)
				{
					$this->log->add('Custom field ' . $fieldName . ' is not assigned to given category');
				}

				$query->clear()
					->delete($this->db->quoteName('#__fields_values'))
					->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
					->where($this->db->quoteName('item_id') . ' = ' . (int) $id);
				$this->db->setQuery($query)->execute();
				$this->log->add('Removed existing custom field values');

				$importValues = explode('|', $this->getState($fieldName, ''));

				$query->clear()
					->insert($this->db->quoteName('#__fields_values'))
					->columns($this->db->quoteName(array('field_id', 'item_id', 'value')));

				foreach ($importValues as $fieldValues)
				{
					$query->values(
						(int) $fieldId . ',' .
						(int) $id . ',' .
						$this->db->quote($fieldValues)
					);
				}

				$this->db->setQuery($query)->execute();
				$this->log->add('Custom field values added');
			}
		}

		return true;
	}

	/**
	 * Load the necessary tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function loadTables()
	{
		$this->content = $this->getTable('Content');
	}

	/**
	 * Clear the loaded tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function clearTables()
	{
		$this->content->reset();
	}

	/**
	 * Set the attributes field.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setAttributes()
	{
		// Check for attributes
		if (!$this->getState('attribs', false))
		{
			$attributeFields = array
			(
				'show_title',
				'link_titles',
				'show_intro',
				'show_category',
				'link_category',
				'show_parent_category',
				'link_parent_category',
				'show_author',
				'link_author',
				'show_create_date',
				'show_modify_date',
				'show_publish_date',
				'show_item_navigation',
				'show_icons',
				'show_print_icon',
				'show_email_icon',
				'show_vote',
				'show_hits',
				'show_noauth',
				'urls_position',
				'alternative_readmore',
				'article_layout',
				'show_publishing_options',
				'show_article_options',
				'show_urls_images_backend',
				'show_urls_images_frontend'
			);

			// Get Value from content plugin
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			// Fire the plugin to get attributes to import
			$pluginFields = $dispatcher->trigger(
				'getAttributes',
				array(
					'extension' => 'joomla',
					'operation' => 'content',
					'log'       => $this->log
				)
			);

			if (!empty($pluginFields[0]))
			{
				$this->log->add('Attributes added for content swmap plugin', false);
				$attributeFields = array_merge($attributeFields, $pluginFields[0]);
			}

			// Load the current attributes
			$attributes = json_decode($this->content->attribs);

			if (!is_object($attributes))
			{
				$attributes = new \stdClass;
			}

			foreach ($attributeFields as $field)
			{
				if (!$this->getState($field, false))
				{
					if ($this->$field == '*')
					{
						$attributes->$field = '';
					}
					else
					{
						$attributes->$field = $this->getState($field, '');
					}
				}
				else
				{
					$attributes->$field = $this->getState($field, '');
				}
			}

			// Store the new attributes
			$this->setState('attribs', json_encode($attributes));
		}
	}

	/**
	 * Set the images.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setImages()
	{
		// Image handling
		$imageHelper = new \CsviHelperImage($this->template, $this->log, $this->csvihelper);

		if (!$this->getState('images'))
		{
			$imageFields = array
			(
				'image_intro',
				'float_intro',
				'image_intro_alt',
				'image_intro_caption',
				'image_fulltext',
				'float_fulltext',
				'image_fulltext_alt',
				'image_fulltext_caption'
			);

			// Load the current images
			$images = json_decode($this->content->images);

			if (!is_object($images))
			{
				$images = new \stdClass;
			}

			foreach ($imageFields as $field)
			{
				if ($field === 'image_intro' || $field === 'image_fulltext')
				{
					// Image handling
					$imgPath = $this->template->get('file_location_image_files', 'images/');

					// Make sure the final slash is present
					if (substr($imgPath, -1) !== '/')
					{
						$imgPath .= '/';
					}

					$imagesContent = array();

					if ($this->getState($field, false))
					{
						$imagesContent[$field] = $this->getState($field, '');
					}

					foreach ($imagesContent as $keyField => $image)
					{
						$fileDetails = array();

						if ($imageHelper->isRemote($image))
						{
							$original = $image;
							$fullPath = $imgPath;
						}
						else
						{
							// Check if the image contains the image path
							$dirname = dirname($image);

							if (strpos($imgPath, $dirname) !== false)
							{
								// Collect rest of folder path if it is more than image default path
								$imageLeftPath = str_replace($imgPath, '', $dirname . '/');
								$image         = basename($image);

								if ($imageLeftPath)
								{
									$image = $imageLeftPath . $image;
								}
							}

							$original = $imgPath . $image;

							// Get subfolders
							$pathParts = pathinfo($original);
							$fullPath  = $pathParts['dirname'] . '/';
						}

						if ($this->template->get('process_image', false))
						{
							$fileDetails = $imageHelper->processImage($original, $fullPath);
						}
						else
						{
							$fileDetails['exists']      = true;
							$fileDetails['isimage']     = $imageHelper->isImage(JPATH_SITE . '/' . $original);
							$fileDetails['name']        = $image;
							$fileDetails['output_name'] = basename($image);
							$fileDetails['output_path'] = $fullPath;
						}

						if ($fileDetails['exists'])
						{
							$processedImage = (empty($fileDetails['output_path'])) ? $fileDetails['output_name'] : $fileDetails['output_path'] . $fileDetails['output_name'];
							$this->setState($keyField, $processedImage);
						}
					}
				}

				if ($this->getState($field, false))
				{
					$images->$field = $this->getState($field, '');
				}
			}

			// Store the new attributes
			$this->setState('images', json_encode($images));
		}
	}

	/**
	 * Set the urls.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setUrls()
	{
		if (!$this->getState('urls'))
		{
			$urlFields = array
			(
				'urla',
				'urlatext',
				'targeta',
				'urlb',
				'urlbtext',
				'targetb',
				'urlc',
				'urlctext',
				'targetc',
			);

			// Load the current images
			$urls = json_decode($this->content->urls);

			if (!is_object($urls))
			{
				$urls = new \stdClass;
			}

			foreach ($urlFields as $field)
			{
				if ($this->getState($field, false))
				{
					$urls->$field = $this->getState($field, '');
				}
			}

			// Store the new attributes
			$this->setState('urls', json_encode($urls));
		}
	}

	/**
	 * Set the meta data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function setMetadata()
	{
		if (!$this->getState('metadata', false))
		{
			$metadataFields = array
			(
				'meta_robots',
				'meta_author',
				'meta_rights',
				'meta_xreference'
			);

			// Load the current attributes
			$metadata = json_decode($this->content->metadata);

			if (!is_object($metadata))
			{
				$metadata = new \stdClass;
			}

			foreach ($metadataFields as $field)
			{
				$newField = str_ireplace('meta_', '', $field);

				if ($this->getState($field, false))
				{
					if ($this->getState($field, '') == '*')
					{
						$metadata->$field = '';
					}
					else
					{
						$metadata->$newField = $this->getState($field, '');
					}
				}
				elseif (!isset($metadata->$newField))
				{
					$metadata->$newField = '';
				}
			}

			// Store the new attributes
			$this->setState('metadata', json_encode($metadata));
		}
	}

	/**
	 * Update Tags data
	 *
	 * @param   int  $id  Id of the article
	 *
	 * @return  bool Returns true if all is OK | Returns false otherwise
	 *
	 * @since   7.7.0
	 */
	private function processTags($id)
	{
		$tags = $this->getState('tags', false);

		if (!$tags)
		{
			return false;
		}

		$tagsArray = explode('|', $tags);

		$typeAlias = 'com_content.article';
		$query     = $this->db->getQuery(true)
			->select($this->db->quoteName('type_id'))
			->from($this->db->quoteName('#__content_types'))
			->where($this->db->quoteName('type_alias') . '  = ' . $this->db->quote($typeAlias));
		$this->db->setQuery($query);
		$typeId  = $this->db->loadResult();
		$tagDate = $this->date->toSql();

		foreach ($tagsArray as $tag)
		{
			$query->clear()
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__tags'))
				->where($this->db->quoteName('path') . '  = ' . $this->db->quote($tag));
			$this->db->setQuery($query);
			$tagId = $this->db->loadResult();
			$this->log->add('Get the tag id ');

			if (!$tagId)
			{
				$this->log->add('No tag id found for the tag ' . $tag, false);
				continue;
			}

			// Delete the values and do a fresh import to avoid dulicate error
			$query->clear()
				->delete($this->db->quoteName('#__contentitem_tag_map'))
				->where($this->db->quoteName('content_item_id') . ' = ' . (int) $id)
				->where($this->db->quoteName('tag_id') . ' = ' . (int) $tagId);
			$this->db->setQuery($query)->execute();
			$this->log->add('Removed existing tag for content before inserting');

			$query->clear()
				->insert($this->db->quoteName('#__contentitem_tag_map'))
				->columns($this->db->quoteName(array('type_alias', 'content_item_id', 'tag_id', 'tag_date', 'type_id')))
				->values($this->db->quote($typeAlias) . ', ' . (int) $id . ', ' .
					(int) $tagId . ', ' . $this->db->quote($tagDate) . ', ' . (int) $typeId);
			$this->db->setQuery($query)->execute();
			$this->log->add('Insert the new tag for content');
		}

		return true;
	}

	/**
	 * Process associated content
	 *
	 * @param   int $contentId Id of the content
	 *
	 * @return  bool True if all ok False otherwise.
	 *
	 * @since   7.10.0
	 */
	private function processAssociations($contentId)
	{
		if ($this->getState('language', false) === '*')
		{
			$this->log->add('Association cannot be added if language is set to all', false);
			$this->log->addStats('incorrect', 'COM_CSVI_LANGUAGE_SET_TO_ALL');

			return false;
		}

		$associations                                     = array();
		$associatedContents                               = explode('|', $this->getState('associations', false));
		$associationsContext                              = 'com_content.item';
		$associations[$this->getState('language', false)] = $contentId;

		foreach ($associatedContents as $content)
		{
			$associatedDetails = explode('#', $content);

			if (strpos($associatedDetails[0], '-') === false)
			{
				$this->log->add('Not a valid language ' . $associatedDetails[0], false);
				$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_LANGUAGE_NOT_VALID', $associatedDetails[0]));
				continue;
			}

			$languageTags         = explode('-', $associatedDetails[0]);
			$languageTags[1]      = strtoupper($languageTags[1]);
			$language             = implode('-', $languageTags);
			$contentAlias         = $associatedDetails[1];
			$associatedContenttId = $this->helper->getContentId($contentAlias, $this->getState('catid', false));

			if ($associatedContenttId)
			{
				$associations[$language] = (int) $associatedContenttId;
			}
			else
			{
				$this->log->add('No associated content id found with alias  ' . $contentAlias, false);
				$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_NO_ARTICLE_FOUND', $contentAlias));
			}
		}

		// Make sure there are no duplicates
		$associations = array_unique($associations);

		if ((count($associations)) > 1)
		{
			$key   = md5(json_encode($associations));
			$query = $this->db->getQuery(true)
				->insert($this->db->quoteName('#__associations'));

			foreach ($associations as $arrayVal => $id)
			{
				$this->deleteAssociation($id, $associationsContext);
				$query->values(((int) $id) . ',' . $this->db->quote($associationsContext) . ',' . $this->db->quote($key));
			}

			$this->db->setQuery($query)->execute();
			$this->log->add('Associations added for content');
		}

		return true;
	}

	/**
	 * Delete a association
	 *
	 * @param   int  $id       Id of the content
	 * @param   int  $context  Context of com_content
	 *
	 * @return  boolean True if deleted | False if id is missing.
	 *
	 * @since   7.10.0
	 */
	private function deleteAssociation($id, $context)
	{
		if (!$id)
		{
			$this->log->add('Association id not found to delete', false);

			return false;
		}

		$query = $this->db->getquery(true)
			->delete($this->db->quotename('#__associations'))
			->where($this->db->quotename('id') . ' = ' . (int) $id)
			->where($this->db->quotename('context') . ' = ' . $this->db->quote($context));
		$this->db->setquery($query)->execute();
		$this->log->add('Association content deleted');

		return true;
	}
}
