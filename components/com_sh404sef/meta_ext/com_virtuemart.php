<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author          Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2015
 * @package         sh404SEF
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version         4.5.1.2666
 * @date            2015-07-08
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// only for 200
$shPageInfo = Sh404sefFactory::getPageInfo();
if (!empty($shPageInfo->httpStatus) && $shPageInfo->httpStatus != 200)
{
	return;
}

$app = JFactory::getApplication();
$sefConfig = Sh404sefFactory::getConfig();

// request
$view = $app->input->getCmd('view', '');
$virtuemart_product_id = $app->input->getInt('virtuemart_product_id');
$virtuemart_category_id = $app->input->getInt('virtuemart_category_id');

// our globals. Yep.
global $shCustomTitleTag, $shCustomDescriptionTag, $shCustomKeywordsTag, $shCustomLangTag, $shCustomRobotsTag, $shCanonicalTag;

// utility
if (!function_exists('vmGetCategoryTitles'))
{
	function vmGetCategoryTitles($id, $selector)
	{
		$title = array();
		if (!empty($id))
		{
			$sefConfig = Sh404sefFactory::getConfig();
			$catModel = VmModel::getModel('category');

			$categoryNames = array();
			$rawTitle = '';
			$cat = $catModel->getCategory($id);
			if (!empty($cat->virtuemart_category_id))
			{
				$rawTitle = empty($cat->customtitle) ? $cat->category_name : $cat->customtitle;
			}

			// current
			if ($selector != shSEFConfig::CAT_NONE)
			{
				if (!empty($rawTitle))
				{
					$categoryNames[] = $rawTitle;
				}

				if ($selector != shSEFConfig::CAT_LAST)
				{
					$currentCatId = $id;
					while ($currentCatId > 0)
					{
						$cat = $catModel->getParentCategory($currentCatId);
						$currentCatId = $cat->virtuemart_category_id;
						if (!empty($currentCatId))
						{
							$categoryNames[] = empty($cat->customtitle) ? $cat->category_name : $cat->customtitle;
						}
					}
				}
			}

			if (!empty($categoryNames))
			{
				//var_dump($categoryNames);die();
				switch ($selector)
				{
					case shSEFConfig::CAT_ALL_NESTED_CAT:
						break;
					case shSEFConfig::CAT_NONE:
						$categoryNames = array();
						break;
					case shSEFConfig::CAT_FIRST:
						$categoryNames = array(array_pop($categoryNames));
						break;
					case shSEFConfig::CAT_LAST:
						$categoryNames = array(array_shift($categoryNames));
						break;
					case shSEFConfig::CAT_2_FIRST:
						while (count($categoryNames) > 2)
						{
							array_shift($categoryNames);
						}
						break;
					case shSEFConfig::CAT_2_LAST:
						while (count($categoryNames) > 2)
						{
							array_pop($categoryNames);
						}
						break;
					default:
						throw new Sh404sefExceptionDefault('Invalid configuration option (' . print_r($id) . ') passed to ' . __METHOD__ . ' in ' . __CLASS__,
							500);;
						break;
				}

				$title = $categoryNames;
			}
		}
		return array('full' => $title, 'raw' => array($rawTitle));
	}
}

if (!function_exists('vmGetShopName'))
{
	function vmGetShopName()
	{
		$title = '';
		$sefConfig = Sh404sefFactory::getConfig();
		// append shop name?
		if ($sefConfig->shVmInsertShopName)
		{
			$vendorModel = VmModel::getModel('vendor');
			$vendor = $vendorModel->getVendor();
			if (!empty($vendor) && !empty($vendor->vendor_store_name))
			{
				$title = $vendor->vendor_store_name;
			}
		}
		return $title;
	}
}

// calculate page title
$title = array();
switch ($view)
{
	case 'category':
		if (!empty($virtuemart_category_id))
		{
			$titles = vmGetCategoryTitles($virtuemart_category_id, $sefConfig->vmWhichVmCategoryTitleCat);
			// if setting set to none, use category title for category pages
			$title = empty($titles['full']) ? $titles['raw'] : $titles['full'];
		}

		// optionally append shopname
		$shopName = vmGetShopName();
		if (!empty($shopName))
		{
			$title[] = $shopName;
		}
		// build title
		$shCustomTitleTag = implode(' ' . $sefConfig->pageTitleSeparator . ' ', $title);

		break;

	case 'productdetails':
		if (!empty($virtuemart_product_id))
		{
			// figure out category
			if (empty($virtuemart_category_id))
			{
				$categoryRecord = ShlDbHelper::selectObject('#__virtuemart_product_categories', '*', array('virtuemart_product_id' => $virtuemart_product_id), $aWhereData = array(), array('ordering' => 'asc'));
				if (!empty($categoryRecord) && !empty($categoryRecord->virtuemart_category_id))
				{
					// we have a category, lets fetch its alias
					$virtuemart_category_id = $categoryRecord->virtuemart_category_id;
				}
			}

			$categoryTitle = vmGetCategoryTitles($virtuemart_category_id, $sefConfig->vmWhichVmProductDetailsTitleCat);
			if (!empty($categoryTitle))
			{
				$title = $categoryTitle['full'];;
			}

			// product data
			// Test VmConfig::$vmlang existence, some people are running really old VM
			if (!empty(VmConfig::$vmlang))
			{
				$productRecord = ShlDbHelper::selectObject('#__virtuemart_products_' . VmConfig::$vmlang, array('product_name', 'customtitle'), array('virtuemart_product_id' => $virtuemart_product_id));
				if (!empty($productRecord))
				{
					$productTitle = empty($productRecord->customtitle) ? $productRecord->product_name : $productRecord->customtitle;
					array_unshift($title, $productTitle);
				}
			}

			// optionally append shopname
			$shopName = vmGetShopName();
			if (!empty($shopName))
			{
				$title[] = $shopName;
			}

			// build title
			$shCustomTitleTag = implode(' ' . $sefConfig->pageTitleSeparator . ' ', $title);
		}
		break;
}
