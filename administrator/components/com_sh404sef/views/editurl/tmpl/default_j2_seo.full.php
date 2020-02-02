<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<table class="adminlist">
  <tbody>
  
    <tr>
      <td width="20%" class="key">
        <label for="metatitle">
          <?php echo JText::_('COM_SH404SEF_META_TITLE'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <input class="text_area" type="text" name="metatitle" id="metatitle" size="90" value="<?php echo $this->escape( $this->meta->metatitle); ?>" />
      </td>
      <td width="10%">
      <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_META_TITLE')); ?>
      </td>
    </tr>
    
    <tr>
      <td width="20%" class="key">
        <label for="metadesc">
          <?php echo JText::_('COM_SH404SEF_META_DESC'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <textarea class="text_area" name="metadesc" id="metadesc" cols="51" rows="5"><?php echo $this->escape( $this->meta->metadesc); ?></textarea>
      </td>
      <td width="10%">
      <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_META_DESC')); ?>
      </td>
    </tr>

    <?php if($this->home): ?>
	    <tr>
		    <td width="20%" class="key">
			    <label for="canonical">
				    <?php echo JText::_('COM_SH404SEF_CANONICAL'); ?>&nbsp;
			    </label>
		    </td>
		    <td width="70%">
			    <input class="text_area" type="text" size="90" id="canonical" name="canonical" value="<?php echo $this->escape( $this->meta->canonical ); ?>" />
		    </td>
		    <td width="10%">
			    <?php
				    echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_CANONICAL'));
			    ?>
		    </td>
	    </tr>
    <?php endif; ?>
    <tr>
      <td width="20%" class="key">
        <label for="metakey">
          <?php echo JText::_('COM_SH404SEF_META_KEYWORDS'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <textarea class="text_area" name="metakey" id="metakey" cols="51" rows="3"><?php echo $this->escape( $this->meta->metakey); ?></textarea>
      </td>
      <td width="10%">
      <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_META_KEYWORDS')); ?>
      </td>
    </tr>

    <tr>
      <td width="20%" class="key">
        <label for="metarobots">
          <?php echo JText::_('COM_SH404SEF_META_ROBOTS'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <input class="text_area" type="text" name="metarobots" id="metarobots" size="30" value="<?php echo $this->escape( $this->meta->metarobots); ?>" />
      </td>
      <td width="10%">
      <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_META_ROBOTS')); ?>
      </td>
    </tr>
    
    <tr>
      <td width="20%" class="key">
        <label for="metalang">
          <?php echo JText::_('COM_SH404SEF_META_LANG'); ?>&nbsp;
        </label>
      </td>
      <td width="70%">
        <input class="text_area" type="text" name="metalang" id="metalang" size="30" value="<?php echo $this->escape( $this->meta->metalang); ?>" />
      </td>
      <td width="10%">
      <?php echo JHTML::_('tooltip', JText::_('COM_SH404SEF_TT_META_LANG')); ?>
      </td>
    </tr>

  </tbody>  
</table>
