<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
* This code comes from: http://stackoverflow.com/questions/4664517/how-do-you-convert-a-parent-child-adjacency-table-to-a-nested-set-using-php-an
* based on: 
/**


--
-- Table structure for table `adjacent_table`
--

DROP TABLE IF EXISTS `adjacent_table`;
CREATE TABLE IF NOT EXISTS `adjacent_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `father_id` int(11) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `adjacent_table`
--

INSERT INTO `adjacent_table` (`id`, `father_id`, `category`) VALUES
(1, 0, 'Books'),
(2, 0, 'CD''s'),
(3, 0, 'Magazines'),
(4, 1, 'Hard Cover'),
(5, 1, 'Large Format'),
(6, 3, 'Vintage');

--
-- Table structure for table `nested_table`
--

DROP TABLE IF EXISTS `nested_table`;
CREATE TABLE IF NOT EXISTS `nested_table` (
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`lft`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `rgt` (`rgt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

*/

    /**
     * @class   tree_transformer
     * @author  Paul Houle, Matthew Toledo
     * @created 2008-11-04
     * @url     http://gen5.info/q/2008/11/04/nested-sets-php-verb-objects-and-noun-objects/
     */
    class tree_transformer 
    {

        private $i_count;
        private $a_link;
		public $n_link; 

        public function __construct(&$a_link, &$n_link, $i_count) 
        {
            if(!is_array($a_link)) throw new Exception("First parameter should be an array. Instead, it was type '".gettype($a_link)."'");
            $this->i_count = $i_count;
            $this->a_link =& $a_link;
			$this->n_link =& $n_link; 
        }

        public function traverse($i_id) 
        {
            $i_lft = $this->i_count;
            $this->i_count++;

            $a_kid = $this->get_children($i_id);
            if ($a_kid) 
            {
                foreach($a_kid as $a_child) 
                {
                    $this->traverse($a_child);
                }
            }
            $i_rgt=$this->i_count;
            $this->i_count++;
            $this->write($i_lft,$i_rgt,$i_id);
        }   

        private function get_children($i_id) 
        {
		    if (!isset($this->a_link[$i_id]))
			 {
			   echo 'a_link empty for id : '.$i_id; 

			 }
            return $this->a_link[$i_id];
        }

        private function write($i_lft,$i_rgt,$i_id) 
        {
			$a_source =& $this->a_link[$i_id]; 

            // root node?  label it unless already labeled in source table
            if (1 == $i_lft && empty($a_source['virtuemart_category_id']))
            {
                $a_source['virtuemart_category_id'] = 'ROOT';
            }
 
			$this->n_link[$i_id]['lft'] = $i_lft; 
			$this->n_link[$i_id]['rgt'] = $i_rgt; 
        }
    }

    
    
