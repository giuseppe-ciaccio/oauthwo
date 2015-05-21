<?php

class Delegation_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initResourceLoader() {
        $this->_resourceLoader->addResourceType('mapper', 'models/Mapper', 'Mapper');
    }

}

