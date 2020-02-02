<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
if ($item->fib) {
    $this->stack[] = $item->parent;
    $this->level = count($this->stack);
}
if ($this->up) {
    while ($this->level > $item->level) {
        echo '</dl>';
        $this->endItem(array_pop($this->stack)); //dd close
        $this->level = count($this->stack);
    }
    $this->up = false;
}
$classes = array(
    'level' . $this->level,
    $this->_classPrefix . $item->id,
    ($item->p ? "parent" : "notparent") ,
    ($item->opened ? "opened" : "") ,
    ($item->active ? "active" : "")
);
if (isset($this->openedlevels[$this->level]) && $item->p) $classes[] = 'opened forceopened';
if ($item->fib) $classes[] = 'first';
if ($item->lib) $classes[] = 'last';
$classes = implode(' ', $classes);
if ($item->fib):
?>
<dl class="level<?php echo $this->level ?>">
<?php
endif; ?>
  <dt class="<?php echo $classes ?>" data-menuid="<?php echo $item->id; ?>">
    <span class="inner">
      <?php echo $item->nname; ?>
    </span>
  </dt>
  <dd class="<?php echo $classes ?>">
  <?php 

if ($item->lib){
    $this->up = true;
}

if(!$this->renderCached || $item->opened){
    if($this->ajax) ob_start();
    echo $content;
    if ($item->p){
        $this->renderItem();
    }else{ 
        $this->endItem($item); //dd close
    }
}else if($this->ajax){
    for($this->pointer; isset($this->items[$this->pointer]) && $this->items[$this->pointer]->level > $item->level;$this->pointer++);
}