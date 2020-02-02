<?php

/**
 *
 * Customfilters ProductsQueryBuilder
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		https://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		see LICENSE.txt
 */

/**
 * The class that builds the products lookup query
 *
 * @author sakis
 *        
 */
class ProductsQueryBuilder
{

    /**
     *
     * @var string
     */
    protected $filtered_products_type = 'all';

    /**
     *
     * @var string
     */
    protected $returned_products_type = 'parent';

    /**
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     *
     * @var \JDatabaseQuery
     */
    protected $query;

    /**
     * Holds all the joins
     *
     * @var array
     */
    protected $joinsMap = [];

    /**
     *
     * @var string
     */
    protected $main_table = '#__virtuemart_products';

    /**
     * Contains the table aliases
     *
     * @var array
     */
    protected $tablesMap = [
        'p' => '#__virtuemart_products',
        'children' => '#__virtuemart_products',
        'p_c' => '#__virtuemart_product_categories',
    	'p_m' => '#__virtuemart_product_manufacturers',
        'p_s' => '#__virtuemart_product_shoppergroups',
        's' => '#__virtuemart_product_shoppergroups',
        'p_p' => '#__virtuemart_product_prices'
    ];

    /**
     *
     * @var string
     */
    protected $currentLangPrefix;

    /**
     *
     * @var string
     */
    protected $defaultLangPrefix;

    /**
     *
     * @param string $filtered_products_type            
     * @param string $returned_products_type            
     */
    public function __construct($filtered_products_type = 'all', $returned_products_type = 'parent')
    {
        $this->filtered_products_type = $filtered_products_type;
        $this->returned_products_type = $returned_products_type;
        $this->currentLangPrefix = cftools::getCurrentLanguagePrefix();
        $this->defaultLangPrefix = cftools::getDefaultLanguagePrefix();
        $this->setTablesMap();
        $this->setQuery();  
        $this->setSelect();
    }
    
    /**
     *
     * @return ProductsQueryBuilder
     */
    protected function setTablesMap()
    {
        $this->tablesMap['l'] = '#__virtuemart_products_' . $this->currentLangPrefix;
        $this->tablesMap['c'] = '#__virtuemart_categories_' . $this->currentLangPrefix;
        $this->tablesMap['m'] = '#__virtuemart_manufacturers_' . $this->defaultLangPrefix;
    
        return $this;
    }

    /**
     * Function that creates/returns the final query
     * @return JDatabaseQuery
     */
    public function create()
    {
        $this->setDefaultWheres();
        return $this->getQuery();
    }

    /**
     * 
     * @return ProductsQueryBuilder
     */
    protected function setDefaultWheres()
    {
        $this->setWhere('p.published', '1');
        
        if ($this->returned_products_type == 'parent') {
        	if($this->filtered_products_type == 'child') {
                $this->setWhere('p.product_parent_id','0','>');
        	}
        	if($this->filtered_products_type == 'parent') {
        		$this->setWhere('p.product_parent_id','0','=');
        	}
        }
        else if($this->returned_products_type == 'child'){
            $this->setWhere('p.product_parent_id','0','>');
        }
        return $this;
    }

    /**
     * Set the query object
     *
     * @return ProductsQueryBuilder
     */
    protected function setQuery()
    {
        if (! isset($this->query)) {
            $this->db = JFactory::getDbo();
            $this->query = $this->db->getQuery(true);
        }
        return $this;
    }

    /**
     *
     * @return JDatabaseQuery
     */
    public function getQuery()
    {
    	if(!isset($this->query)) {
    		$this->setQuery();
    	}
        return $this->query;
    }   

    /**
     * Set the select part of the query
     *
     * @return ProductsQueryBuilder
     */
    public function setSelect()
    {
        $mainTableAlias = array_search($this->main_table, $this->tablesMap);
        
        if ($this->filtered_products_type == 'child' && $this->returned_products_type == 'parent') {
            $this->query->select('DISTINCT SQL_CALC_FOUND_ROWS ' . $mainTableAlias . '.product_parent_id');
        } 
        else if ($this->filtered_products_type == 'all' && $this->returned_products_type == 'parent') {
            $this->query->select('DISTINCT SQL_CALC_FOUND_ROWS (CASE WHEN '.$mainTableAlias.'.product_parent_id>0 THEN '.$mainTableAlias.'.product_parent_id ELSE p.virtuemart_product_id END) AS virtuemart_product_id');
        }
        else {
            $this->query->select('DISTINCT SQL_CALC_FOUND_ROWS ' . $mainTableAlias . '.virtuemart_product_id');
        }
        $this->query->from($this->main_table . ' AS ' . $mainTableAlias);
        
        return $this;
    }

    /**
     * Set the where subqueries to the main query
     *
     * @param string $field            
     * @param mixed $values            
     * @param string $table            
     *
     * @return ProductsQueryBuilder
     */
    public function setWhere($field, $value, $sign = '=' , $table = '')
    {
        if (isset($value)) {
            $is_table = strpos($field, '.');
            if ($is_table !== false) {
                $table = substr($field, 0, $is_table);
                $field = substr($field, $is_table+1);
            }
            if (empty($table)) {
                return $this;
            }
            
            if (is_array($value)) {
            	$newValues = array_map([$this->db, "escape"], $value);
                $this->query->where($table . '.' . $field . ' IN (' . implode(',', $newValues) . ')');
            } else 
                if (is_string($value) || is_numeric($value)) {
                    $this->query->where($table . '.' . $field . $sign . $this->db->quote($value));
                }
            
            $this->setJoin($table);           
        }
        return $this;
    }

    /**
     * Set the query joins
     *
     * @param string $tableAlias            
     * @throws \RuntimeException
     * @return void|ProductsQueryBuilder
     */
    public function setJoin($tableAlias)
    {
    	$tableAlias = trim($tableAlias);
    	$tableAlias = trim($tableAlias, '`');
    	$tableAlias = strtolower($tableAlias);
    	$mainTableAlias = array_search($this->main_table, $this->tablesMap);
        if (isset($this->joinsMap[$tableAlias]) || $tableAlias == $mainTableAlias ||  $tableAlias == $this->main_table) {
            return;
        }
        
        if (! isset($this->tablesMap[$tableAlias]) && strpos($tableAlias, '#') === false) {
            throw new \RuntimeException('The table with alias ' . $tableAlias . ' does not exist');
        }
        
        $tableName = strpos($tableAlias, '#') === false ? $this->tablesMap[$tableAlias] : $tableAlias;
        $mainTableAlias = array_search($this->main_table, $this->tablesMap);
        $this->joinsMap[$tableAlias] = true;
        
        switch ($tableAlias) {
            case 'p_c':
            case 'p_m':
                if ($this->filtered_products_type == 'child' && ($this->returned_products_type == 'child' || $this->returned_products_type == 'parent')) {
                    $this->query->innerJoin($tableName . ' AS ' . $tableAlias . ' 
                    		ON ' . $tableAlias . '.virtuemart_product_id =' . $mainTableAlias . '.product_parent_id');
                } 
                else if($this->filtered_products_type == 'all') {
                	$this->query->innerJoin($tableName . ' AS ' . $tableAlias . '
                    		ON ' . $tableAlias . '.virtuemart_product_id =
                			(CASE WHEN '.$mainTableAlias.'.product_parent_id>0 THEN '.$mainTableAlias.'.product_parent_id ELSE '.$mainTableAlias.'.virtuemart_product_id END)');
                }
                else {
                    $this->query->innerJoin($tableName . ' AS ' . $tableAlias . ' 
                    		ON ' . $tableAlias . '.virtuemart_product_id=' . $mainTableAlias . '.virtuemart_product_id');
                }
                break;
            case 'l':
            	$this->query->leftJoin('#__virtuemart_products_'.$this->currentLangPrefix.' AS l ON p.virtuemart_product_id=l.virtuemart_product_id');
            	break;
            
            case 'c':
            	$this->setJoin('p_c');
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.virtuemart_category_id = p_c.virtuemart_category_id');
                break;
            case 'm':
            	$this->setJoin('p_m');
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.virtuemart_manufacturer_id = p_m.virtuemart_manufacturer_id');
                break;
            case 'p_s':
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.virtuemart_product_id = ' . $mainTableAlias . '.virtuemart_product_id');
               
                break;
            case 's':
            	$this->setJoin('p_s');
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.virtuemart_shoppergroup_id = p_s.virtuemart_shoppergroup_id');                
                break;
            case 'p_p':
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.virtuemart_product_id = ' . $mainTableAlias . '.virtuemart_product_id');
                break;
            case 'children':
                $this->query->leftJoin($tableName . ' AS ' . $tableAlias . '
                		ON ' . $tableAlias . '.product_parent_id = ' . $mainTableAlias . '.virtuemart_product_id');
                break;
        }
        return $this;
    }
    
    /**
     * 
     * @param string $field
     * @param string $direction
     * @return ProductsQueryBuilder
     */
    public function setOrder($field, $direction = 'ASC')
    {
    	$orderBy = [];
    	switch ($field) {
    	    case 'pc.ordering,product_name':
    	        $orderBy = ['p_c.ordering', 'l.product_name'];    	       
    	        break;
    	    case 'product_name':
    	        $orderBy= ['l.product_name', 'p.virtuemart_product_id'];    	       
    	        break;
    	    case 'product_special':
    	    	$this->setWhere('p.product_special', 1);    	       
    	        $orderBy = [ 'RAND()'];
    	        break;
    	    case 'product_s_desc':
    	        $orderBy =  ['l.product_s_desc'];    	       
    	        break;
    	    case 'category_name':
    	        $orderBy = ['c.`category_name`'];    	       
    	        break;
    	    case 'category_description':
    	        $orderBy = ['c.`category_description`'];    	       
    	        break;
    	    case 'mf_name':
    	        $orderBy = ['m.`mf_name`'];    	       
    	        break;
    	    case 'ordering':
    	    case 'pc.ordering':
    	        $orderBy = ['p_c.`ordering`'];       
    	        break;
     	    case 'product_price':
                $this->getQuery()->select('IF(p_p.override, p_p.product_override_price, p_p.product_price) AS product_price');                
                $orderBy = [
                    'product_price'
                ];
                $this->setJoin('p_p');
                break;
    	    case 'created_on':
    	    case '`p`.created_on':
    	        $orderBy = ['p.`created_on`'];
    	        break;
    	    case 'product_mpn':
    	        $orderBy = ['p.`product_mpn`'];
    	        break;
    	    default ;
    	    if(!empty($field)){
    	        $orderBy = [$field];
    	    } else {    	       
    	        $orderBy= [''];
    	    }
    	    break;
    	}
    	
    	//set the joins    	
    	if(count($orderBy)>0) {
    		foreach ($orderBy as &$field) {
    			$table_field = explode('.', $field);
    			if(count($table_field)>1) {
    				$this->setJoin($table_field[0]);
    			}
    			$field.= ' '.$direction;
    		}
    	}
    	$orderByString = implode(',', $orderBy);
    	if(!empty($orderByString)) {
    	   $this->getQuery()->order($this->db->escape($orderByString));
    	}
    	return $this;
    }
}


