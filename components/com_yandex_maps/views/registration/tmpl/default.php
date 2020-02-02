<?php defined("_JEXEC") or die;
$params = $this->params;
if (!isset($organization)) {
	$organization = JModelLegacy::getInstance( 'Organizations', 'Yandex_MapsModel');
}
if (!jFactory::getApplication()->isAdmin()) {
	if (!$params->get('registration_organization_active', 1) or ($params->get('registration_organization_active', 1)==2 and !JFactory::getUser()->id)) { ?>
		<h1>Регистрация новых точек запрещена <?php echo $params->get('registration_organization_active', 1)==2 ? '<small>Для не зарегестрированных пользователей <a href="'.jRoute::_('index.php?option=com_users&view=login').'">Войти</a></small>' : ''?></h1>
	<?php 
	return;
	} ?>
	<?php if (!jhtml::_('xdwork.isajax')){ ?>
	<h1>Регистрация</h1>
	<?php } ?>
	<form id="xdsoft_registration_organization_save" action="<?php echo jURI::root()?>index.php?option=com_yandex_maps&task=registration.save" method="post" enctype="multipart/form-data">
<?php include "iconpicker.php";?>
<?php } ?>
<link rel="stylesheet" href="<?php echo JURI::root()?>media/com_yandex_maps/css/registration.css">
<?php if ($params->get('registration_organization_form', 2) or $params->get('registration_organization_name', 2)) {?>
<fieldset>
	<legend><span>Название организации</span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_form', 2)) {?>
			<div class="span6 control-group">
				<label>Организационно-правовая форма</label>
				<input class="validate <?php echo $params->get('registration_organization_form', 2)==2 ? 'required' : ''?>" data-rules="short_string" name="jform[organization_form]" id="jform_organization_form" value="<?php echo htmlspecialchars(@$organization->organization_form)?>" maxsize="50" type="text" placeholder="Например: ООО"/>
				<div class="xdsoft_tooltip ">Заполните поле</div>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_name', 2)) {?>
			<div class="span6 control-group ">
				<label>Название</label>
				<input class="validate <?php echo $params->get('registration_organization_name', 2)==2 ? 'required' : ''?>" data-rules="name" name="jform[organization_name]" id="jform_organization_name"  value="<?php echo htmlspecialchars(@$organization->organization_name)?>" type="text" placeholder="Например: Аптечный дом"/>
				<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
			</div>
			<?php } ?>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_lider_fio', 2) 
		or $params->get('registration_organization_acting_basis', 2)
		or $params->get('registration_organization_acting_basis_number', 2)
		or $params->get('registration_organization_acting_basis_date', 2)
		or $params->get('registration_organization_lider_position', 2)
) {?>
<fieldset>
	<legend><span>Руководитель организации </span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_lider_fio', 2)) {?>
			<div class="span6 control-group">
				<label>ФИО полностью</label>
				<input class="validate <?php echo $params->get('registration_organization_lider_fio', 2)==2 ? 'required' : ''?>" data-rules="fio" name="jform[organization_lider_fio]"  value="<?php echo htmlspecialchars(@$organization->organization_lider_fio)?>" id="jform_organization_lider_fio" type="text" placeholder="Например: Иванов Иван Иванович"  value="<?php echo htmlspecialchars(@$organization->organization_form)?>" />
				<div class="xdsoft_tooltip">Фамилия, Имя и Отчество должны быть введены полностью</div>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_lider_position', 2)) {?>
			<div class="span6 control-group">
				<label>Должность руководителя</label>
				<input class="validate <?php echo $params->get('registration_organization_lider_position', 2)==2 ? 'required' : ''?>" data-rules="name" name="jform[organization_lider_position]" id="jform_organization_lider_position" value="<?php echo htmlspecialchars(@$organization->organization_lider_position)?>" type="text" placeholder="Например: Директор"/>
				<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
			</div>
			<?php } ?>
		</div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_acting_basis', 2)) {?>
			<div class="span6 control-group">
				<label>Действует на основании</label>
				<select class="" name="jform[organization_acting_basis]" id="jform_organization_acting_basis">
					<option  <?php echo $organization->organization_acting_basis == 0 ? 'selected' : ''?>  value="0">Устава</option>
					<option  <?php echo $organization->organization_acting_basis == 1 ? 'selected' : ''?> value="1">Доверенности</option>
					<option  <?php echo $organization->organization_acting_basis == 2 ? 'selected' : ''?> value="2">Приказа</option>
				</select>
			</div>
			<?php } ?>
			<div class="span6">
				<div id="jform_organization_acting_basis_params" style="display:none">
					<?php if ($params->get('registration_organization_acting_basis_number', 2)) {?>
					<div class="control-group">
						<label>Номер</label>
						<input class="validate <?php echo $params->get('registration_organization_acting_basis_number', 2)==2 ? 'required' : ''?>" data-rules="license" name="jform[organization_acting_basis_number]"  value="<?php echo htmlspecialchars(@$organization->organization_acting_basis_number)?>" id="jform_organization_acting_basis_number"  type="text" placeholder="Например: 123456789"/>
						<div class="xdsoft_tooltip">Введено неверно. Возможно содержит недопустимые символы</div>
					</div>
					<?php } ?>
					<?php if ($params->get('registration_organization_acting_basis_date', 2)) {?>
					<div class="control-group">
						<label>Дата</label>
						<input class="validate date <?php echo $params->get('registration_organization_acting_basis_date', 2)==2 ? 'required' : ''?>" data-rules="date" name="jform[organization_acting_basis_date]"   value="<?php echo htmlspecialchars(@$organization->organization_acting_basis_date)?>" id="jform_organization_acting_basis_date"  type="text" placeholder="Например: 12.12.2009"/>
						<div class="xdsoft_tooltip">Введено неверно. Возможно не верный формат даты</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if (($params->get('registration_organization_type', 1) and $params->get('registration_organization_type_variants', ''))
		or $params->get('registration_organization_trademark', 2)
) { ?>
<fieldset>
	<legend><span>Тип объекта</span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_type', 1) and $params->get('registration_organization_type_variants', '')) {?>
			<div class="span6 control-group">
				<label>Тип объекта</label>
				<select class="" name="jform[organization_type]" id="jform_organization_type">
					<?php
					$types = explode(',', $params->get('registration_organization_type_variants', ''));
					foreach ($types as $type) { ?>
						<option <?php echo @$organization->organization_type==$type ? 'selected' : ''?>><?php echo $type;?></option>
					<?php }	?>
				</select>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_trademark', 2)) {?>
			<div class="span6 control-group">
				<label>Торговая марка</label>
				<input class="validate <?php echo $params->get('registration_organization_trademark', 2)==2 ? 'required' : ''?>" data-rules="name" name="jform[organization_trademark]"  value="<?php echo htmlspecialchars(@$organization->organization_trademark)?>" id="jform_organization_trademark" type="text" placeholder="Например: Таблетка"/>
				<div class="xdsoft_tooltip">Введите название Торговой марки. Возможно введенное название слишком короткое.</div>
			</div>
			<?php } ?>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_contact_fio', 2) 
		or $params->get('registration_organization_contact_phone', 2)
		or $params->get('registration_organization_contact_position', 2)
) { ?>
<fieldset>
	<legend><span>Контактное лицо</span></legend>
	<div>
		<div class="row-fluid">
			<div class="span6 ">
				<?php if ($params->get('registration_organization_contact_fio', 2)) {?>
				<div class="control-group">
					<label>ФИО</label>
					<input class="validate <?php echo $params->get('registration_organization_contact_fio', 2)==2 ? 'required' : ''?>" data-rules="fio" name="jform[organization_contact_fio]" value="<?php echo htmlspecialchars(@$organization->organization_contact_fio)?>" id="jform_organization_contact_fio" type="text" placeholder="Например: Иванов Иван Иванович"/>
					<div class="xdsoft_tooltip">Фамилия, Имя и Отчество должны быть введены полностью</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_organization_contact_phone', 2)) {?>
				<div class="control-group">
					<label>Телефон</label>
					<input data-format="<?php echo $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##')?>" class="phone validate <?php echo $params->get('registration_organization_contact_phone', 2)==2 ? 'required' : ''?>" data-rules="phone" name="jform[organization_contact_phone]" value="<?php echo htmlspecialchars(@$organization->organization_contact_phone)?>" id="jform_organization_contact_phone" type="text" placeholder="Например: <?php echo preg_replace_callback('/#/', function () {
                        return rand(0, 9);
                    }, $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##'))?>"/>
					<div class="xdsoft_tooltip">Введите номер телефона в следующем формате <?php echo $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##')?></div>
				</div>
				<?php } ?>
			</div>
			<?php if ($params->get('registration_organization_contact_position', 2)) {?>
			<div class="span6 control-group">	
				<label>Должность</label>
				<input class="validate <?php echo $params->get('registration_organization_contact_position', 2)==2 ? 'required' : ''?>" data-rules="name" name="jform[organization_contact_position]" value="<?php echo htmlspecialchars(@$organization->organization_contact_position)?>" id="jform_organization_contact_position"  type="text" placeholder="Например: Заместитель заведующего"/>
				<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
			</div>
			<?php } ?>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_address', 2) 
		or $params->get('registration_organization_legal_address', 2) ) { ?>
<fieldset>
	<legend><span>Местоположение</span></legend>
	<div>
		<div class="row-fluid">
			<?php 
				$var_adreses = array('_legal'=>'Адрес юридический', ''=>'Адрес фактический',);
				foreach ($var_adreses as $type=>$label) {
					if ($params->get('registration_organization'.$type.'_address', 1)) {
					?> 
					<div class="span6" id="form_organization_address<?php echo $type?>">
						<h4><?php echo $label?></h4>
						<?php switch ($params->get('registration_organization_address_input_view',1)) {
							case 1:
								include 'address_in_oneline.php';
							break;
							case 0:
								include 'address_in_parts.php';
							break;
						}?>
					</div>
				<?php }
			} ?>
		</div>
		<?php if ($params->get('registration_organization_address', 2) and $params->get('registration_organization_legal_address', 2) and $params->get('registration_organization_fact_and_legal_equal', 2)) {?>
		<label class="checkbox">
		  <input id="organization_fact_and_legal_equal" name="jform[organization_fact_and_legal_equal]" value="1" type="checkbox">Фактический совпадают с юридическим
		</label>
		<?php } ?>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_phone', 2)) {?>
<fieldset>
	<legend><span>Телефон для справок</span></legend>
	<div class="control-group">
		<label>Номер</label>
		<input data-format="<?php echo $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##')?>" class="phone validate <?php echo $params->get('registration_organization_phone', 2)==2 ? 'required' : ''?>" data-rules="phone" name="jform[organization_phone]"  value="<?php echo htmlspecialchars(@$organization->organization_phone)?>" id="jformorganization__phone" type="text" placeholder="Например: <?php echo preg_replace_callback('/#/', function () {
            return rand(0, 9);
        }, $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##'))?>"/>
		<div class="xdsoft_tooltip">Введите номер телефона в следующем формате <?php echo $params->get('registration_organization_phone_format', '+7 (9##) ###-##-##')?></div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_shedule_24', 2) 
		or $params->get('registration_organization_shedule_days', 2)
		or $params->get('registration_organization_start_in', 2)
) { ?>
<fieldset>
	<legend><span>График работы</span></legend>
	<div>
		<?php if ($params->get('registration_organization_self_schedule', 1)) {?>
			<div class="row-fluid" id="self_schedule">
				<div class="span8">
					<div class="control-group">
						<label>График работы в произвольном формате</label>
						<textarea rows="10" class="validate <?php echo $params->get('registration_organization_self_schedule', 2)==2 ? 'required' : ''?>" data-rules="string" name="jform[organization_self_schedule_text]" id="jform_organization_self_schedule_text" placeholder="Введите свой график работы. Пример: Пн-Птн - с 9-00 до 18-00
Сб - с 9-00 до 15-00
Вск - выходной"><?php echo htmlspecialchars(@$organization->organization_self_schedule_text)?></textarea>
						<div class="xdsoft_tooltip">Значение должно быть не короче 3-х символов</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
		<div class="row-fluid" id="standart_schedule">
			<?php if ($params->get('registration_organization_shedule_24', 1)) {?>
			<div class="span3">
				<label class="checkbox">
					<input   <?php echo (@$organization->organization_shedule_24) ? 'checked' : ''?>  name="jform[organization_shedule_24]" id="jform_organization_shedule_24" value="1" type="checkbox">Круглосуточно
				</label>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_shedule_days', 2)) {?>
			<div class="span5 control-group">
				<div class="btn-group btn-radio" data-toggle="buttons-checkbox">
					<?php 
					$values = $organization->organization_shedule_days ? explode(',', @$organization->organization_shedule_days) : array();
					$days = explode(',', 'пн,вт,ср,чт,пт,сб,вск');
					foreach($days as $i=>$day) { ?>
						<button type="button" class="btn btn-small  <?php echo ($i > 4 ? 'btn-warning' : 'btn-success')?>"><?php echo $day?><input style="display:none" type="checkbox" class="shedule_days <?php echo $params->get('registration_organization_shedule_days', 2)==2 ? 'required' : ''?>" name="jform[organization_shedule_days][]" id="jform_organization_shedule_days<?php echo $i?>" value="<?php echo $day?>" <?php echo in_array($day, $values) ? 'checked' : ''?>/></button>
					<?php } ?>
				</div>
				<div class="xdsoft_tooltip">Выберите хотя бы одно значение</div>
			</div>
			<?php } ?>
			<div class="span4">
				<?php if ($params->get('registration_organization_start_in', 2)) {?>
				<div id="jform_organization_period_params" class="control-group">
					<span>c</span>
					<input min="0" max="23" name="jform[organization_start_in]" value="<?php echo htmlspecialchars(@$organization->organization_start_in)?>" id="jform_organization_start_in" type="number" class="validate <?php echo $params->get('registration_organization_start_in', 2)==2 ? 'required' : ''?>" data-rules="number" placeholder="00-23">
					<span>по</span>
					<input min="0" max="23" name="jform[organization_end_in]" value="<?php echo htmlspecialchars(@$organization->organization_end_in)?>" id="jform_organization_end_in" type="number" class="validate <?php echo $params->get('registration_organization_start_in', 2)==2 ? 'required' : ''?>" data-rules="number" placeholder="00-23">
					<div class="xdsoft_tooltip">Возможные чичсловые значения от 00 до 23</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_service_delivery', 2) 
		or ($params->get('registration_organization_service_delivery_variants', 2) and $params->get('registration_organization_service_delivery_variants_list', ''))
) { ?>
<fieldset>
	<legend><span>Доставка</span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_service_delivery', 2)) {?>
			<div class="span3">
				<label class="checkbox">
					<input name="jform[organization_service_delivery]" <?php echo (@$organization->organization_service_delivery) ? 'checked' : ''?> value="1" id="jform_organization_service_delivery" type="checkbox">Услуги по доставке
				</label>
			</div>
			<?php } ?>
			<div class="span6">
				<?php if ($params->get('registration_organization_service_delivery_variants', 2) and $params->get('registration_organization_service_delivery_variants_list', '')) {?>
				<div id="jform_organization_delivery_params" class="control-group" style="display:none">
					<div class="btn-group btn-radio" data-toggle="buttons-checkbox">
						<?php
						$values = $organization->organization_service_delivery_variants ? explode(',', @$organization->organization_service_delivery_variants) : array();
						$types = explode(',', $params->get('registration_organization_service_delivery_variants_list', ''));
						foreach ($types as $i=>$type) { ?>
							<button type="button" class="btn btn-small btn-success"><?php echo $type?><input style="display:none" type="checkbox" name="jform[organization_service_delivery_variants][]" class="organization_service_delivery_variants <?php echo $params->get('registration_organization_service_delivery_variants', 2)==2 ? 'required' : ''?>" id="organization_service_delivery_variants1" value="<?php echo $i+1;?>" <?php echo in_array($i+1, $values) ? 'checked' : ''?>/></button>
						<?php }	?>
					</div>
					<div class="xdsoft_tooltip">Выберите хотя бы одно значение</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_license_number', 2) 
		or $params->get('registration_organization_license_date', 2)
) { ?>
<fieldset>
	<legend><span>Лицензия</span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_license_number', 2)) {?>
			<div class="span6 control-group">
				<label>Номер</label>
				<input class="validate <?php echo $params->get('registration_organization_license_number', 2)==2 ? 'required' : ''?>" data-rules="license" name="jform[organization_license_number]"  value="<?php echo htmlspecialchars(@$organization->organization_license_number)?>"  id="jform_organization_license_number" type="text" placeholder="Например: 11111111"/>
				<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_license_date', 2)) {?>
			<div class="span6 control-group">
				<label>Дата</label>
				<input class="date validate <?php echo $params->get('registration_organization_license_date', 2)==2 ? 'required' : ''?>" data-rules="date" name="jform[organization_license_date]"  value="<?php echo htmlspecialchars(@$organization->organization_license_date)?>"  id="jform_organization_license_date" type="text" placeholder="Например: 12.05.2007"/>
				<div class="xdsoft_tooltip">Введено неверно. Возможно не верный формат даты</div>
			</div>
			<?php } ?>
		</div>
	</div>
</fieldset>
<?php } ?>
<?php if ($params->get('registration_organization_bank', 1)) { ?>
<fieldset>
	<legend><span>Банковские реквизиты</span></legend>
	<div>
		<div class="row-fluid">
			<div class="span6">
				<?php if ($params->get('registration_organization_bank_inn', 2)) {?>
				<div class="control-group">
					<label>ИНН</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_inn', 2)==2 ? 'required' : ''?>" data-rules="number" name="jform[organization_bank_inn]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_inn)?>"  id="jform_organization_bank_inn" type="number" placeholder="Например: 12121212"/>
					<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_organization_bank_kpp', 2)) {?>
				<div class="control-group">
					<label>КПП</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_kpp', 2)==2 ? 'required' : ''?>" data-rules="number" name="jform[organization_bank_kpp]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_kpp)?>" id="jform_organization_bank_kpp" type="number" placeholder="Например: 23232323"/>
					<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_organization_bank_rs', 2)) {?>
				<div class="control-group">
					<label>Расчётный счёт</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_rs', 2)==2 ? 'required' : ''?>" data-rules="number" name="jform[organization_bank_rs]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_rs)?>" id="jform_organization_bank_rs" type="number" placeholder="Например: 42727727277212121212"/>
					<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
				</div>
				<?php } ?>
			</div>
			<div class="span6">
				<?php if ($params->get('registration_organization_bank_name', 2)) {?>
				<div class="control-group">
					<label>Название банка</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_name', 2)==2 ? 'required' : ''?>" data-rules="bankname" name="jform[organization_bank_name]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_name)?>" id="jform_organization_bank_name" type="text" placeholder="Например: Сбербанк"/>
					<div class="xdsoft_tooltip">Введено неверно</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_bank_ks', 2)) {?>
				<div class="control-group">
					<label>Корреспондентский счёт</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_ks', 2)==2 ? 'required' : ''?>" data-rules="number" name="jform[organization_bank_ks]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_ks)?>" id="jform_organization_bank_ks" type="number" placeholder="Например: 42727727277212121212"/>
					<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_organization_bank_bik', 2)) {?>
				<div class="control-group">
					<label>БИК</label>
					<input class="validate <?php echo $params->get('registration_organization_bank_bik', 2)==2 ? 'required' : ''?>" data-rules="number" name="jform[organization_bank_bik]"  value="<?php echo htmlspecialchars(@$organization->organization_bank_bik)?>" id="jform_organization_bank_bik" type="number" placeholder="Например: 1212121212"/>
					<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</fieldset>
<?php } ?>

<?php if ($params->get('registration_organization_aditional_info', 1)==1 or ($params->get('registration_organization_aditional_info', 1) and !JFactory::getUser()->id)) {?>
<fieldset>
	<legend><span>Дополнительные данные</span></legend>
	<div>
		<div class="row-fluid">
			<?php if ($params->get('registration_organization_email', 2) and ($params->get('registration_organization_email', 2)!=3 or !JFactory::getUser()->id)) {?>
			<div class="span6 control-group">
				<label>e-mail</label>
				<input class="email validate <?php echo $params->get('registration_organization_email', 2) > 1 ? 'required' : ''?>" data-rules="email" name="jform[organization_email]"  value="<?php echo htmlspecialchars(@$organization->organization_email)?>" id="jform_organization_email" type="email" placeholder="Например: myemail@sitename.com"/>
				<div class="xdsoft_tooltip">Введено неверно. электронный ящик должен быть следующего формата name@sitename</div>
			</div>
			<?php } ?>
			<?php if ($params->get('registration_organization_website', 2)) { ?>
			<div class="span6 control-group">
				<label>Сайт</label>
				<input class="website validate <?php echo $params->get('registration_organization_website', 2)==2 ? 'required' : ''?>" data-rules="url" name="jform[organization_website]"   value="<?php echo htmlspecialchars(@$organization->organization_website)?>" id="jform_organization_website" type="url" placeholder="Например: http://sitename.com"/>
				<div class="xdsoft_tooltip">Введено неверно. Не верный адрес URL. Введите полный url сайта, к примеру: https://xdan.ru</div>
			</div>
			<?php } ?>
		</div>
		<?php if ($params->get('registration_organization_image', 2)) { ?>
		<div class="control-group">
			<label>Логотип</label>
			<input class="validate <?php echo $params->get('registration_organization_image', 2)==2 ? 'required' : ''?>" data-rules="file" name="jform[organization_image]" id="jform_organization_image" type="file"/>
			<div class="xdsoft_tooltip">Выберите логотип</div>
		</div>
		<?php } ?>
		<?php if ($params->get('registration_organization_info', 2)) { ?>
		<div class="control-group">
			<label>Дополнительные данные (не более <?php echo $params->get('registration_organization_info_size', 1500)?> знаков)</label>
			<textarea class="maximum validate <?php echo $params->get('registration_organization_info', 2)==2 ? 'required' : ''?>" size="<?php echo $params->get('registration_organization_info_size', 1500)?>" style="min-height:50px;" placeholder="Произвольную информацию для покупателей" name="jform[organization_info]" id="jform_organization_info"><?php echo htmlspecialchars(@$organization->organization_info)?></textarea>
			<div class="xdsoft_tooltip">Введите необходимую информацию</div>
		</div>
		<?php } ?>
		<?php if (!jFactory::getApplication()->isAdmin()) { ?>
			<div class="row-fluid">
				<?php if ($params->get('registration_organization_password1', 2) and !JFactory::getUser()->id) {?>
				<div class="span6 control-group">
					<label>Пароль</label>
					<input class="validate required" data-rules="password" name="jform[organization_password1]" id="jform_organization_password1" type="password"/>
					<div class="xdsoft_tooltip">Пароль должен быть не короче 6-ти символов, и должен содержать латинские или русские символы (прописные или строчные) а также цифры</div>
				</div>
				<?php } ?>
				<?php if ($params->get('registration_organization_password1', 2) and $params->get('registration_organization_password2', 2) and !JFactory::getUser()->id) {?>
				<div class="span6 control-group">
					<label>Повторите пароль</label>
					<input class="validate required" data-rules="password2" name="jform[organization_password2]" id="jform_organization_password2" type="password"/>
					<div class="xdsoft_tooltip">Пароли не совпадают</div>
				</div>
				<?php } ?>
		</div>
		<?php } ?>
	</div>
</fieldset>
<?php } ?>
<?php if (!jFactory::getApplication()->isAdmin()) { ?>
	<?php if ($params->get('registration_organization_oferta', 2) ){ ?>
	<fieldset>
		<div class="control-group">
			<label class="checkbox">
				<input value="1" class="validate <?php echo $params->get('registration_organization_oferta', 2)==2 ? 'required' : ''?>" data-rules="checkbox" id="jform_organization_oferta" type="checkbox">Согласен с <a href="<?php echo $params->get('registration_organization_oferta_link', '#')?>">Условиями публичной оферты</a>
			</label>
			<div class="xdsoft_tooltip">Для продолжения регистрации, вы должны согласиться с условиями</div>
		</div>
	</fieldset>
	<?php } ?>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="map_id" value="<?php echo jFactory::getApplication()->input->get('map_id', 0, 'INT')?>"/>
	<input type="hidden" name="zoom" value="<?php echo jFactory::getApplication()->input->get('zoom', 0, 'INT')?>"/>
	<input type="hidden" name="lat" value="<?php echo jhtml::_('xdwork.coordinate', jFactory::getApplication()->input->get('lat', 0, 'RAW'))?>"/>
	<input type="hidden" name="lan" value="<?php echo jhtml::_('xdwork.coordinate', jFactory::getApplication()->input->get('lan', 0, 'RAW'))?>"/>
	<button id="submit_button" type="submit" class="btn btn-large btn-success disabled"><i class="icon icon-save"></i>&nbsp;&nbsp;Зарегистрировать</button>
	<button id="reset_button" type="reset" class="btn btn-large btn-warning"><i class="icon icon-trash"></i>&nbsp;&nbsp;Очистить</button>
	</form>
<?php } ?>