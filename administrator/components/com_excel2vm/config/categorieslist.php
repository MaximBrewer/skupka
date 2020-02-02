<?php
  /**
 * @package jlvklike
 * @author Anton Voynov (anton@joomline.net)
 * @version 1.2
 * @copyright (C) 2010 by Anton Voynov(http://www.joomline.net)
 * @license JoomLine: http://joomline.net/licenzija-joomline.html
 *
*/

defined('JPATH_BASE') or die();
jimport('joomla.form.formfield'); 

class JFormFieldCategorieslist extends JFormField{
	    protected     $type = 'categorieslist';
        public        $list=array();
        protected function getInput(){
                $options=$this->getCategoryList();

                if($options){
                        return JHTML::_('select.genericlist',$options, $this->name, ' size="1"', 'category_child_id', 'category_name',$this->value);
                }
                else{
                  return "Категории не найдены";
                }
        }

        function getCategoryList($parent_id=0,$prefix='|_ '){
    	if($parent_id==0)$this->list[]=JHTML::_('select.option',  '0', 'Все', 'category_child_id', 'category_name' );
           $db = JFactory::getDBO();
           try{
               $db->setQuery("SELECT cc.category_child_id, category_name
    								  FROM #__virtuemart_category_categories as cc
    								  LEFT JOIN  #__virtuemart_categories_ru_ru as c ON c.virtuemart_category_id = cc.category_child_id
    								  WHERE category_name IS NOT NULL AND category_name !='' AND cc.category_parent_id ='$parent_id'
    								  ORDER BY cc.category_child_id");
    		   $categories=$db->loadObjectList('category_child_id');
           }
           catch(Exception $e){
              $db->setQuery("SELECT cc.category_child_id, category_name
    								  FROM #__virtuemart_category_categories as cc
    								  LEFT JOIN  #__virtuemart_categories_en_gb as c ON c.virtuemart_category_id = cc.category_child_id
    								  WHERE category_name IS NOT NULL AND category_name !='' AND cc.category_parent_id ='$parent_id'
    								  ORDER BY cc.category_child_id");
    		  $categories=$db->loadObjectList('category_child_id');
           }


		   if(!$categories)
			 	return false;

		   foreach($categories as $id => $cat){
                 $this->list[]=JHTML::_('select.option',  $id,(!$parent_id?'':$prefix).$cat->category_name, 'category_child_id', 'category_name' );
                 $this->getCategoryList($id,'&nbsp;.&nbsp;'.$prefix);
		   }
           return $this->list;
    }
}