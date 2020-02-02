<?php
/**
 /**
 * The helper class which contains the functionality for fetching and creating the filter's options
 * @package	customfilters
 * @author 	Sakis Terz
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

class CustomfiltersConfig
{

    /**
     *
     * @var \Registry
     */
    protected $parameters;

    /**
     * Config instance container
     *
     * @var \CustomfiltersConfig
     */
    protected static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setParameters();
    }

    /**
     *
     * @param string $type            
     * @param string $module            
     * @return Config
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new CustomfiltersConfig();
        }
        
        return self::$instance;
    }

    /**
     *
     * @return Config
     */
    protected function setParameters()
    {
        if (! isset($this->parameters)) {
            $this->parameters = cftools::getComponentparams();
        }
        return $this;
    }

    /**
     * Return the type of products that should be searched (parent, child, all)
     *
     * @return string
     */
    public function getFilteredProductsType()
    {       
        // filters from
        $filtered_products = $this->parameters->get('filtered_products', 'parent');
        $searchable = $filtered_products;
        
        return $searchable;
    }

    /**
     * Return the type of products that should be returned (parent, child, all), based on the filtered products setting
     *
     * @return string
     */
    public function getReturnedProductsType()
    {
        // return parent or child products
        $returned_products = $this->parameters->get('returned_products', 'parent');
        
        // filters from
        $filtered_products = $this->getFilteredProductsType();
        
        /*
         * In case we return child products, these should be searched
         */
        if ($filtered_products == 'child') {
            if ($returned_products == 'all') {
                $returned_products = $filtered_products;
            }
        }
        
        if ($filtered_products == 'parent') {
            $returned_products = $filtered_products;
        }
        
        return $returned_products;
    }

    /**
     * Magic method that calls other methods
     *
     * @param string $method            
     * @param array $args            
     * @throws \RuntimeException
     */
    public function get($param, $default)
    {
        return $this->parameters->get($param, $default);
    }
}