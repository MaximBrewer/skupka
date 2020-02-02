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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_improved_ajax_login/assets/css/improved_ajax_login.css');
?>
<style>
.icon-48-oauth {
  background: url(<?php echo JURI::root()
    ?>administrator/components/com_improved_ajax_login/assets/images/l_oauths.png);
  height: 47px;
}
#system-message ul {
  margin-left: 0;
}
fieldset.adminform textarea,
fieldset.adminform input[type=text] {
  width: 200px;
}
/* Temporary fix for drifting editor fields */
.adminformlist li {
  clear: both;
}
</style>
<script type="text/javascript">
    function getScript(url,success) {
        var script = document.createElement('script');
        script.src = url;
        var head = document.getElementsByTagName('head')[0],
        done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState
                || this.readyState == 'loaded'
                || this.readyState == 'complete')) {
                done = true;
                success();
                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            }
        };
        head.appendChild(script);
    }
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',function() {
        jQuery(document).ready(function(){


            Joomla.submitbutton = function(task)
            {
                if (task == 'oauth.cancel') {
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
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_improved_ajax_login&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="oauth-form" class="form-validate">
    <div class="width-40 fltlft">
        <fieldset class="adminform">
            <legend><?php echo $this->item->name ?></legend>
            <ul class="adminformlist">

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
				<li><?php echo $this->form->getLabel('app_id'); ?>
				<?php echo $this->form->getInput('app_id'); ?></li>
				<li><?php echo $this->form->getLabel('app_secret'); ?>
				<?php echo $this->form->getInput('app_secret'); ?></li>
				<li><label><b>Create App</b></label>
        <?php $createApp = $this->item->alias == 'windows' ? 'https://account.live.com/developers/applications' : $this->item->create_app ?>
				<a href="<?php echo $createApp ?>" target="_blank"><?php echo $createApp ?></a>
        <br />Click above, log in and follow the tutorial</li>
				<li><label>App/Site domain</label>
				<input type="text" value="<?php echo $this->item->alias == 'windows' ? 'https://'.$_SERVER['SERVER_NAME'].JURI::root(true) : JURI::root() ?>" style="cursor:text" readonly="readonly" onclick="this.select()" /></li>
				<li><label>Redirect URI</label>
				<textarea rows="4" style="cursor:text" readonly="readonly" onclick="this.select()"><?php
          switch ($this->item->alias) {
            case 'windows': echo 'https://'.$_SERVER['SERVER_NAME'].rtrim(JURI::root(true), '/').'/index.php'; break;
            case 'vk':
            case 'twitter': echo JURI::root().'index.php'; break;
            default: echo JURI::root().'index.php?option=com_improved_ajax_login&task='.$this->item->alias;
          }
        ?></textarea></li>
				<input type="hidden" name="jform[auth]" value="<?php echo $this->item->auth; ?>" />
				<input type="hidden" name="jform[token]" value="<?php echo $this->item->token; ?>" />
				<input type="hidden" name="jform[userinfo]" value="<?php echo $this->item->userinfo; ?>" />
            </ul>
        </fieldset>
    </div>
    <div class="width-60 fltlft" id="tutor">
      <fieldset class="adminform">
        <legend>Tutorial</legend>
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

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>
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