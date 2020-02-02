<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Editor Advanced Poll buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.article
 */
class plgButtonSL_AdvPoll extends JPlugin {
	/**
	 * Constructor
	 *
	 * @access		protected
	 * @param		object	$subject The object to observe
	 * @param		array	$config  An array that holds the plugin configuration
	 */
	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	function onDisplay($name) {
		/*
		 * Javascript to insert the link
		 * View element calls jSelectArticle when an article is clicked
		 * jSelectArticle creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectPoll(id, title, catid, editor) {
			var title	= prompt('" . JText::_('PLG_ARTICLE_BUTTON_SL_ADVPOLL_INPUT_TITLE') . "');
			var tag		= '{sl_advpoll id=\'' + id + '\' width=\'250\' center=\'1\' title=\'' + title + '\'}';
			jInsertEditorText(tag, editor);
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_sl_advpoll&amp;view=polls&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1&e_name=' . $name;

		$button = new JObject();
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_ARTICLE_BUTTON_SL_ADVPOLL');
		$button->name = 'file-add';
		$button->options = "{handler: 'iframe', size: {x: 770, y: 400}}";

		return $button;
	}
}
