<?php
    class App_Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract {
        public function init () {
            $config = $this->getOptions ();
            $config = App_LoadConfig::file ($config ['config']);
            
            $auth = Zend_Auth::getInstance ();
            $acl = new App_Acl ($config);

            $fc = Zend_Controller_Front::getInstance ();
            $fc->registerPlugin (new App_Controller_Plugin_FilterAccess ($acl, $auth));
            return $config;
	    }
	}