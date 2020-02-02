<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsModelOrganizations extends CModel{
	public $error = array();
	private $_condition;
	public $table = '#__yandex_maps_organizations';
	public $primary_id = 'organization_object_id';

	public function defaults() {
		if (!$this->organization_address) {
			$this->organization_address = '{"full":"","zoom":"","lat":"","lan":""}';
		}
		if (!$this->organization_address_legal) {
			$this->organization_address_legal = '{"full":"","zoom":"","lat":"","lan":""}';
		}
	}

	public function validate() {
		return !count($this->error);
	}
}