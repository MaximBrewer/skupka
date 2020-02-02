<?php

defined('_JEXEC') or die('Restricted access');
$view=JRequest :: getVar('view','excel2vm','','string');

?>
<style type="text/css">
.component_version_date{
	font-weight: bold;
    color: #000099;
    text-decoration: underline;
    font-size: 14px;
}

.component_version{
    font-size: 14px;
}
</style>
<?php $support_time=@strtotime($this->data->support);  ?>
<div id="container">
    <div style="width: 45%;float:left">
          <h2>Тех. поддержка</h2>
          <h3>Последняя версия компонента - <?php echo  @$this->data->version ?></h3>
          <h3>Установленная у Вас версия&nbsp;&nbsp;&nbsp;&nbsp; - <?php echo  @$this->my_version ?>
              <?php if(@$this->data->version!='Невозможно получить данные' AND @$this->data->version!=$this->my_version): ?>
                 <?php if($support_time>time()): ?>
                 <form action="" method="POST">
                     <input type="hidden" name="option" value="com_excel2vm">
                     <input type="hidden" name="view" value="support">
                     <input type="hidden" name="task" value="update">
                     <?php echo  JHtml::_('form.token'); ?>
                     <input name="" type="submit" value="Обновить до последней версии">
                 </form>
                 <?php else: ?>
                     <a target="_blank" href="http://php-programmist.ru/shop/order/support.html?order_id=<?php echo  $this->order_id ?>"><span style="color:red"> Необходимо продлить подписку</span></a>
                 <?php endif; ?>
              <?php else: ?>
                 <span style="color:green"> Установлена последняя версия</span>
              <?php endif; ?>
          </h3>
          <?php
          if($support_time){
              $this->data->support=date("d.m.Y",$support_time);
          }
          ?>
          <h3>Поддержка и обновления доступны до - <span style="color:<?php echo $support_time<time()?"red":"green" ?>"><?php echo  @$this->data->support ?></span> <?php echo  ($support_time>0 AND $support_time<time())?'<a target="_blank" href="http://php-programmist.ru/shop/order/support.html?order_id='.$this->order_id.'"><input name="" type="button" value="Продлить подписку"></a>':'' ?></h3>
          <h2>Сообщите тех. поддержке о проблемах и сложностях, которые у Вас возникли:</h2>
          <form action="" method="POST">
                     <textarea name="message" style="width:90%;" rows="6"></textarea>
                     <input type="hidden" name="option" value="com_excel2vm">
                     <input type="hidden" name="view" value="support">
                     <input type="hidden" name="task" value="send_message">
                     <?php echo  JHtml::_('form.token'); ?>
                     <input name="" type="submit" value="Отправить сообщение в поддержку">
          </form>

          <h1>Видео-инструкция:</h1>
          <h2>1.Базовые настройки:</h2>
          <iframe width="560" height="315" src="//www.youtube.com/embed/C8H26Yvc9Xw" frameborder="0" allowfullscreen></iframe>
          <br>
          <h2>2.Способы указать, в какие категории помещать товары во время импорта:</h2>
          <iframe width="560" height="315" src="//www.youtube.com/embed/mru3iOz1eQk" frameborder="0" allowfullscreen></iframe>
          <br>
          <h2>3.Настраиваемые поля, не влияющие на цену:</h2>
          <iframe width="560" height="315" src="//www.youtube.com/embed/Z2gqahP2ePc" frameborder="0" allowfullscreen></iframe>
          <br>
          <h2>4.Настраиваемые поля, влияющие на цену (Атрибуты корзины):</h2>
          <iframe width="560" height="315" src="//www.youtube.com/embed/QcBeyGyC7DY" frameborder="0" allowfullscreen></iframe>
          <br>
          <h2>5.Настраиваемые поля Virtuemart 2 Multiple Customfields Filter 2 :</h2>
          <iframe width="560" height="315" src="//www.youtube.com/embed/5Q80ix5Enfg" frameborder="0" allowfullscreen></iframe>

    </div>
    <div style="width: 50%;float:left; padding-left: 20px;">
          <h2>Список изменений</h2>
          <?php echo  $this->changelist? $this->changelist:"Не удалось получить список изменений. Возможно на Вашем хостинге не работает функция file_get_contents()" ?>

    </div>
</div>



