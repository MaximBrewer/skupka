<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/improved_ajax_login.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){

    });

    Joomla.submitbutton = function(task)
    {
        if(task == 'oauth.cancel'){
            Joomla.submitform(task, document.getElementById('oauth-form'));
        }
        else{

            if (task != 'oauth.cancel' && document.formvalidator.isValid(document.id('oauth-form'))) {

                Joomla.submitform(task, document.getElementById('oauth-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    };
</script>

<form action="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="oauth-form" class="form-validate">
    <div class="row-fluid">
        <div class="span4 form-horizontal">
            <fieldset class="adminform">
            <legend><?php echo $this->item->name; ?></legend>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('app_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('app_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('app_secret'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('app_secret'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><b>Create App</b></div>
				<div class="controls">
          <?php $createApp = $this->item->alias == 'windows' ? 'https://account.live.com/developers/applications' : $this->item->create_app ?>
  				<a href="<?php echo $createApp ?>" target="_blank"><?php echo $createApp ?></a>
          <br />Click above, log in and follow the tutorial
        </div>
			</div>
			<div class="control-group">
				<div class="control-label">App/Site domain</div>
				<div class="controls">
          <input type="text" value="<?php echo $this->item->alias == 'windows' ? 'https://'.$_SERVER['SERVER_NAME'].JURI::root(true) : JURI::root() ?>" style="cursor:text" readonly="readonly" onclick="this.select()" />
        </div>
			</div>
			<div class="control-group">
				<div class="control-label">Redirect URI</div>
				<div class="controls">
          <textarea rows="4" style="cursor:text" readonly="readonly" onclick="this.select()"><?php
            switch ($this->item->alias) {
              case 'windows': echo 'https://'.$_SERVER['SERVER_NAME'].rtrim(JURI::root(true), '/').'/index.php'; break;
              case 'vk':
              case 'twitter': echo JURI::root().'index.php'; break;
              default: echo JURI::root().'index.php?option=com_improved_ajax_login&task='.$this->item->alias;
            }
          ?></textarea>
        </div>
			</div>

            </fieldset>
        </div>
<div class="span2"></div>
<div id="tutor" class="span6">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Tutorial'); ?></legend>
    <table class="admintable" width="100%">
  		<tr>
  			<td align="center">
  				<h4>
            <a href="javascript:tutorialPrev()">&lt;&lt; Prev</a>
            &nbsp;&nbsp;&nbsp;Step <span id="step">0</span>&nbsp;&nbsp;&nbsp;
            <a href="javascript:tutorialNext()">Next &gt;&gt;</a>
          </h4>
  			</td>
  		</tr>
  		<tr>
  			<td align="center">
  				<img id="tutorial" style="width:100%; float:none"/>
  			</td>
  		</tr>
		</table>
	</fieldset>
</div>
<script type="text/javascript">
window.tutorialWidth = new Array();
window.tutorialPath = "<?php echo $tutorial_path = JURI::base().'components/com_improved_ajax_login/assets/images/tutorials/'.$this->item->id; ?>";
window.tutorialMax = 0;
function tutorialNext() {
  var step = document.getElementById('step'),
      num = Number(step.innerHTML),
      pic = document.getElementById('tutorial');
  if (num < tutorialMax) {
    pic.src = window.tutorialPath+'/'+(++num)+'.gif';
    pic.style.maxWidth = window.tutorialWidth[num]+"px";
    step.innerHTML = num;
  } else {
    var img = new Image();
    img.onload = function(e) {
      if (window.tutorialMax == 0) document.getElementById('tutor').style.display="block";
      step.innerHTML = window.tutorialMax = num;
      pic.src = e.currentTarget.src;
      pic.style.maxWidth = (window.tutorialWidth[num]=e.currentTarget.width)+'px'
      delete img;
    };
    img.src = window.tutorialPath+'/'+(++num)+'.gif';
  }
}
tutorialNext();
function tutorialPrev() {
  var step = document.getElementById('step');
  var num = Number(step.innerHTML);
  var pic = document.getElementById('tutorial');
  if (num > 1) {
    pic.src = window.tutorialPath+'/'+(--num)+'.gif';
    pic.style.maxWidth = window.tutorialWidth[num]+"px";
    step.innerHTML = num;
  }
}
</script>


        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>