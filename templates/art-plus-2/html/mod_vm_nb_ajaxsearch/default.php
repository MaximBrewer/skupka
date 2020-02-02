<?php

/*------------------------------------------------------------------------

 * JoomNB VM Ajax Search Module

* author    Netbase Team

* copyright Copyright (C) 2012 http://netbase.vn. All Rights Reserved.

* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

* Websites: http://www.netbase.vn

* Technical Support:  Forum - http://www.netbase.vn

-------------------------------------------------------------------------*/

// no direct access

defined('_JEXEC') or die('Restricted access');

$category_id = JRequest::getVar('virtuemart_category_id');

?>

<!--BEGIN Search Box -->

<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&search=true&limitstart=0&virtuemart_category_id='.$category_id ); ?>" method="get">

	<div class="search<?php echo $params->get('moduleclass_sfx'); ?>">

		<input type="text" id="product_suggest" placeholder="Поиск..."

				data-max-item="<?php echo $max_items?>" 

				data-min-chars="<?php echo $min_chars?>" 

				data-scroll="false" 

				data-scroll-height="400" 

				name="keyword"/>

		<input type="submit" value="Search" class="cms-submit" onclick="this.form.keyword.focus();"/> 

	</div>

	<input type="hidden" name="limitstart" value="0" />

	<input type="hidden" name="option" value="com_virtuemart" />

	<input type="hidden" name="view" value="category" />

</form>



<!-- End Search Box -->

