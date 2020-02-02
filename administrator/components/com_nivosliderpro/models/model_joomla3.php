<?php

jimport('joomla.application.component.modeladmin');

abstract class UniteNivoModel extends JModelAdmin {

    protected function prepareTable($table) {
        $this->prepareTableReal($table);
    }

}

?>