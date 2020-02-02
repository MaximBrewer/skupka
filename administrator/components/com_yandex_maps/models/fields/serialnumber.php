<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
class JFormFieldSerialNumber extends JFormField{
	public function attr($attr_name, $default = null){
		if (isset($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
	function getInput() {
        if (!empty($this->value) and preg_match('#^[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}$#', trim($this->value))) {
            $servers = jfactory::getDBO()->setQuery('select * from #__update_sites where location like "https://xdan.ru/update/%"')->loadObjectList();
            foreach ($servers as $server) {
                $server->location = preg_replace('#([0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4})?\.xml$#', trim($this->value).'.xml', $server->location);
                jfactory::getDBO()->updateObject('#__update_sites', $server, 'update_site_id');
            }
        }
		$return = '<input type="text" name="'.$this->name.'" value="'.$this->value.'"/>';
        if (!empty($this->value) and trim($this->value) and !preg_match('#^[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}\-[0-9A-Z]{4}$#', $this->value)) {
            $return.= '<div style="color:red">Вы ввели код в неверном формате. Для получения кода, напишите на <a href="mailto:chupurnov@gmail.com">chupurnov@gmail.com</a> с того электронного ящика, который казали при покупке. Код активации должен иметь такой формат: A2CD-E3G1-HI9K-M0LM-OP12</div>';
        }
        return $return ;
	}
}
