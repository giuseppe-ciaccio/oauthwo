<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bootstrap
 *
 * @author andou
 */
class Resourceset_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function _initRegistration() {

        $route = new Zend_Controller_Router_Route('resourceset/:action',
                        array('module'=>'resourceset','controller' => 'registration'));

        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();
        $router->addRoute('resourcesetModuleRoute', $route);
    }

}

