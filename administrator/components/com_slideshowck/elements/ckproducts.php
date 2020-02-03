<?php

/**
 * @copyright	Copyright (C) 2017 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('JPATH_PLATFORM') or die;

require_once 'ckformfield.php';

class JFormFieldCkproducts extends CKFormField {

	protected $type = 'ckproducts';

	protected function getLabel() {
		return '';
	}

	protected function getInput() {
		$html = '<style>
.ckproduct {
	padding: 10px 20px;
	display: inline-block;
	color: #1f496e;
	border: 1px solid #1f496e;
	margin: 3px;
	border-radius: 2px;
	transition: 0.3s all;
}
.ckproduct:hover {
	background: #1f496e;
	color: #fff;
	text-decoration: none;
}
</style>
			<h3>' . JText::_('SLIDESHOWCK_VISIT_OTHER_PRODUCTS') . '</h3>
			<div>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/accordeonmenu-ck">Accordeon Menu CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/beautiful-ck">Beautiful CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/carousel-ck">Carousel CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/download-joomla-extensions/view_category/37-cookies-ck">Cookies CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/floating-module-ck">Floating Module CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/image-effect-ck">Image Effect CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/maximenu-ck">Maximenu CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/mediabox-ck">Mediabox CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/menu-manager-ck">Menu Manager CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/extensions-joomla/mobile-menu-ck">Mobile Menu CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/modules-manager-ck">Modules Manager CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/page-builder-ck">Page Builder CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/en/joomla-extensions/scroll-to-ck">Scroll To CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr/extensions-joomla/tooltip-gc">Tooltip CK</a>
					<a class="ckproduct" target="_blank" href="https://www.template-creator.com">Template Creator CK</a>
					<a class="ckproduct" target="_blank" href="https://www.joomlack.fr">And more...</a>
				</div>';

		return $html;
	}
}