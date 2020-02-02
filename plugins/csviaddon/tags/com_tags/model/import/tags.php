<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaContacts
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

namespace tags\com_tags\model\import;

defined('_JEXEC') or die;

/**
 * Tags import.
 *
 * @package     CSVI
 * @subpackage  JoomlaTags
 * @since       7.7.0
 */
class Tags extends \RantaiImportEngine
{
	/**
	 * CSVI fields
	 *
	 * @var    \CsviHelperImportFields
	 * @since  7.7.0
	 */
	protected $fields;

	/**
	 * The addon helper
	 *
	 * @var    \Com_TagsHelperCom_Tags
	 * @since  7.7.0
	 */
	protected $helper;

	/**
	 * Fields table
	 *
	 * @var    \TagsTableTags
	 * @since  7.7.0
	 */
	private $tagsTable;

	/**
	 * Start the menu import process.
	 *
	 * @return  bool  True on success | false on failure.
	 *
	 * @since   7.7.0
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 * @throws  \UnexpectedValueException
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
					case 'published':
						switch (strtolower($value))
						{
							case '-2':
								$value = -2;
								break;
							case 'y':
							case 'yes':
							case '1':
								$value = 1;
								break;
							default:
								$value = 0;
								break;
						}

						$this->setState($name, $value);
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		// If no alias set use the title field
		$alias = $this->getState('alias', false);
		$title = $this->getState('title', false);
		$path  = $this->getState('path', false);

		// Create alias from name field
		if ($title && !$alias)
		{
			$alias = \JFilterOutput::stringURLSafe($title);
			$this->setState('alias', $alias);
		}

		if ((!$alias || !$title) && !$path)
		{
			$this->loaded = false;
			$this->log->addStats('skipped', \JText::_('COM_CSVI_NO_NAME_ALIAS_FIELDS_FOUND'));
		}
		else
		{
			$this->loaded = true;

			if (!$this->getState('id', false))
			{
				$this->setState('id', $this->helper->getTagId($this->getState('alias', ''), $this->getState('path', '')));
			}

			if ($this->tagsTable->load($this->getState('id', 0)))
			{
				if (!$this->template->get('overwrite_existing_data'))
				{
					$this->log->add(\JText::sprintf('COM_CONTACTS_WARNING_OVERWRITING_SET_TO_NO', $this->getState('alias')), false);
					$this->loaded = false;
				}
			}
		}

		return true;
	}

	/**
	 * Process a record.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no path or menu ID can be found.
	 *
	 * @since   7.7.0
	 *
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 * @throws  \UnexpectedValueException
	 */
	public function getProcessRecord()
	{
		if (!$this->loaded)
		{
			return false;
		}

		if (!$this->getState('id', false) && $this->template->get('ignore_non_exist'))
		{
			$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->getState('alias', '')));
		}
		else
		{
			if ($this->getState('tag_delete', 'N') === 'Y')
			{
				$this->deleteTags();
			}
			else
			{
				$this->processParams();
				$this->processImage();
				$this->processMetadata();

				if (!$this->getState('access', false))
				{
					$this->setState('access', 1);
				}

				if (!$this->getState('language', false))
				{
					$this->setState('language', '*');
				}

				if (!$this->getState('id', false))
				{
					$this->pathSeparator = $this->template->get('path_separator', '/');
					$paths               = explode($this->pathSeparator, $this->getState('path'));
					$parentId            = false;
					$pathkeys            = array_keys($paths);
					$lastkey             = array_pop($pathkeys);
					$path                = '';

					foreach ($paths as $key => $pathValue)
					{
						if ($key > 0)
						{
							if (strpos($pathValue, $path) === false)
							{
								$path = $pathValue;
							}
							else
							{
								$path .= $this->pathSeparator . $pathValue;
							}
						}
						else
						{
							$path = $pathValue;
						}

						// Check if the path exists
						$pathId = $this->helper->getTagPathId($path);

						if (!$pathId)
						{
							$this->tagsTable->reset();

							$data              = array();
							$data['parent_id'] = 1;

							if ($parentId)
							{
								$data['parent_id'] = $parentId;
							}

							$data['published']       = ($this->getState('published')) ? $this->getState('published') : 1;
							$data['access']          = $this->getState('access');
							$data['language']        = $this->getState('language');
							$data['params']          = $this->getState('params');
							$data['images']          = $this->getState('images');
							$data['urls']            = ($this->getState('urls')) ? $this->getState('urls') : '{}';
							$data['created_time']    = $this->date->toSql();
							$data['publish_up']      = ($this->getState('publish_up')) ? $this->getState('publish_up') : $this->date->toSql();
							$data['created_user_id'] = $this->userId;
							$data['level']           = $key + 1;
							$data['path']            = $path;
							$data['alias']           = $path;
							$data['title']           = $pathValue;

							if ($lastkey === $key)
							{
								$data['title']       = (!$this->getState('title', false)) ? $pathValue : $this->getState('title');
								$data['note']        = $this->getState('note');
								$data['description'] = $this->getState('description');
								$data['alias']       = $this->getState('alias');
							}

							$this->tagsTable->setLocation($data['parent_id'], 'last-child');

							$this->tagsTable->bind($data);

							try
							{
								$this->tagsTable->save($data);
								$this->log->add('Tag data ' . implode(',', $data));
								$this->log->add('Tag added successfully', false);
								$this->log->addStats('added', 'COM_CSVI_TAG_ADDED_SUCCESSFULLY');
								$query = $this->db->getQuery(true)
									->update($this->db->quoteName('#__tags'))
									->set($this->db->quoteName('parent_id') . ' = ' . (int) $data['parent_id'])
									->set($this->db->quoteName('level') . ' = ' . (int) ($key + 1))
									->where($this->db->quoteName('id') . ' = ' . (int) $this->tagsTable->id);
								$this->db->setQuery($query)->execute();
								$parentId = $this->tagsTable->id;
							}
							catch (\Exception $e)
							{
								$this->log->add('Cannot add tag. Error: ' . $e->getMessage(), false);
								$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_TABLE_TAGSTABLE_ERROR', $e->getMessage()));

								return false;
							}
						}
						else
						{
							$parentId = $pathId;
						}
					}
				}
				else
				{
					if ($this->template->get('keeptagid'))
					{
						$tagId = $this->tagsTable->checkId($this->getState('id'));
						$this->setState('id', $tagId);
					}

					$this->tagsTable->load($this->getState('id'));
					$this->tagsTable->modified_user_id = $this->userId;
					$this->tagsTable->modified_time    = $this->date->toSql();
					$this->tagsTable->bind($this->state);

					try
					{
						$this->tagsTable->store();
						$this->log->add('Tag updated successfully', false);
						$this->log->addStats('updated', 'COM_CSVI_TAG_UPDATED_SUCCESSFULLY');
					}
					catch (\Exception $e)
					{
						$this->log->add('Cannot add tag. Error: ' . $e->getMessage(), false);
						$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_TABLE_TAGSTABLE_ERROR', $e->getMessage()));

						return false;
					}

				}
			}
		}

		return true;
	}

	/**
	 * Rebuild the tags left and right values after the import is complete.
	 *
	 * @return  void
	 *
	 * @since   7.7.0
	 */
	public function onAfterStart()
	{
		$this->tagsTable->rebuild(1);
	}

	/**
	 * Delete a tag detail
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	private function deleteTags()
	{
		if ($this->getState('id', false))
		{
			$query = $this->db->getquery(true)
				->delete($this->db->quotename('#__tags'))
				->where($this->db->quotename('id') . ' = ' . (int) $this->getState('id'));
			$this->db->setquery($query);
			$this->log->add('Tag deleted');
			$this->db->execute();

			$query->clear()
				->delete($this->db->quotename('#__contentitem_tag_map'))
				->where($this->db->quotename('tag_id') . ' = ' . (int) $this->getState('id'));
			$this->db->setquery($query);
			$this->log->add('Deleted tag entries for categories and content');
			$this->db->execute();
		}
	}

	/**
	 * Process params field.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	private function processParams()
	{
		if (!$this->getState('params', false))
		{
			$paramsFields = array
			(
				'tag_layout',
				'tag_link_class'
			);

			$params = json_decode($this->tagsTable->params);

			if (!is_object($params))
			{
				$params = new \stdClass;
			}

			foreach ($paramsFields as $field)
			{
				$fieldValue = $this->getState($field, '');

				if (isset($fieldValue))
				{
					$params->$field = $this->getState($field, '');
				}
			}

			// Store the new params
			$this->setState('params', json_encode($params));
		}
	}

	/**
	 * Process metadat fields.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	private function processMetadata()
	{
		if (!$this->getState('metadata', false))
		{
			$metadataFields = array
			(
				'author',
				'robots'
			);

			$metadata = json_decode($this->tagsTable->metadata);

			if (!is_object($metadata))
			{
				$metadata = new \stdClass;
			}

			foreach ($metadataFields as $field)
			{
				$fieldValue = $this->getState($field, '');

				if (isset($fieldValue))
				{
					$metadata->$field = $this->getState($field, '');
				}
			}

			// Store the new metadata
			$this->setState('metadata', json_encode($metadata));
		}
	}

	/**
	 * Process the images data.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	private function processImage()
	{
		if (!$this->getState('images', false))
		{
			$imagesFields = array
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
			$images = json_decode($this->tagsTable->metadata);

			if (!is_object($images))
			{
				$images = new \stdClass;
			}

			foreach ($imagesFields as $field)
			{
				$images->$field = $this->getState($field, '');
			}

			// Store the new images
			$this->setState('images', json_encode($images));
		}
	}

	/**
	 * Load the necessary tables.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	public function loadTables()
	{
		$this->tagsTable = $this->getTable('Tags');
	}

	/**
	 * Clear the loaded tables.
	 *
	 * @return  void.
	 *
	 * @since   7.7.0
	 */
	public function clearTables()
	{
		$this->tagsTable->reset();
	}
}
