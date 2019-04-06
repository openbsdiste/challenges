<?php
    class App_Application_Module_Autoloader extends Zend_Application_Module_Autoloader {
        public function initDefaultResourceTypes () {
            $this->addResourceTypes (array (
                'dbview' => array (
                    'namespace' => 'Model_DbView',
                    'path'      => 'models/DbView',
                ),
                'web' => array (
                    'namespace' => 'Model_Web',
                    'path'      => 'models/Web',
                ),
                'metier' => array (
                    'namespace' => 'Model_Metier',
                    'path'      => 'models/Metier'
                ),
                'enums' => array (
                    'namespace' => 'Model_Enum',
                    'path'      => 'models/enums'
                )
            ));
            parent::initDefaultResourceTypes ();
        }
    }
