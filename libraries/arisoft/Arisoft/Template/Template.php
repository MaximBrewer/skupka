<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Template;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use JFolder;

class Template
{
    private static $filters = array();

	public static function display($templateFile, $data = array())
	{
		include $templateFile;
	}

    public static function parse($template, $params, $removeUnrecognized = false)
    {
        if (empty($params)) return $template;

        $paramsRegExp = '/\{\$([^}\|]+)((\|[^}\|]+)*)}/si';

        $matches = array();
        @preg_match_all($paramsRegExp, $template, $matches, PREG_SET_ORDER);

        if (empty($matches))
            return $template;

        $search = array();
        $replace = array();
        foreach ($matches as $match)
        {
            $value = self::getParamValue($match[1], $params);
            if (is_null($value) && !$removeUnrecognized)
                continue;

            $value = self::applyFilters($value, !empty($match[2]) ? $match[2] : '');

            $search[] = $match[0];
            $replace[] = $value;
        }

        return str_replace($search, $replace, $template);
    }

    public static function isFilterRegistered($name)
    {
        self::loadFilters();

        return isset(self::$filters[$name]);
    }

    private static function loadFilters()
    {
        static $filtersLoaded = false;

        if ($filtersLoaded)
            return ;

        $files = JFolder::files(dirname(__FILE__) . '/Filters', '\.php$', false, false);
        foreach ($files as $file)
        {
            $filter = basename($file, '.php');
            $filterDirective = join('_', array_map('strtolower', preg_split('/(?=[A-Z])/', $filter, -1, PREG_SPLIT_NO_EMPTY)));

            self::$filters[$filterDirective] = array(
                'name' => $filter,
                'instance' => null
            );
        }

        $filtersLoaded = true;
    }

    private static function getFilter($name)
    {
        if (!isset(self::$filters[$name]))
            return null;

        if (!is_null(self::$filters[$name]['instance']))
        {
            return self::$filters[$name]['instance'];
        }

        $filterClass = 'Arisoft\\Template\\Filters\\' . self::$filters[$name]['name'];
        $filter = new $filterClass;
        self::$filters[$name]['instance'] =& $filter;

        return $filter;
    }

    private static function applyFilter($name, $value, $params = null)
    {
        $filter = self::getFilter($name);
        if (is_null($filter))
            return $value;

        return $filter->parse($value, $params);
    }

    private static function applyFilters($value, $filterStr)
    {
        self::loadFilters();

        $filters = explode('|', $filterStr);
        if (empty($filters))
            return $value;

        foreach ($filters as $filter)
        {
            if (empty($filter)) continue;

            $filterInfo = explode(':', $filter);
            $filterName = $filterInfo[0];
            array_shift($filterInfo);

            $value = self::applyFilter($filterName, $value, $filterInfo);
        }

        return $value;
    }

    private static function getParamValue($key, $params)
    {
        $value = null;

        if (!$key)
            return $value;

        $keys = null;
        if (strpos($key, ':') !== false)
        {
            $keys = explode(':', $key);
        }
        else
        {
            $keys = array($key);
        }

        $value = $params;
        foreach ($keys as $cKey)
        {
            $value = ArrayHelper::getValue($value, $cKey, null);

            if (is_null($value))
                break;
        }

        if (is_array($value))
            $value = null;

        return $value;
    }
}