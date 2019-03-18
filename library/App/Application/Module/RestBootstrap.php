<?php
    class App_Application_Module_RestBootstrap extends Zend_Application_Module_Bootstrap {
        public function _initREST () {
            $fc = Zend_Controller_Front::getInstance ();
            $fc->registerPlugin (new App_Controller_Plugin_RestHandler ($fc));
            $cs = new App_Controller_Action_Helper_ContextSwitch ();
            Zend_Controller_Action_HelperBroker::addHelper ($cs);
            $rc = new App_Controller_Action_Helper_RestContexts ();
            Zend_Controller_Action_HelperBroker::addHelper ($rc);
        }
        
        public function getResourceLoader () {
            if ((null === $this->_resourceLoader) && (false !== ($namespace = $this->getAppNamespace ()))) {
                $r = new ReflectionClass ($this);
                $path = $r->getFileName ();
                $aama = new App_Application_Module_Autoloader (array (
                    'namespace' => $namespace,
                    'basePath'  => dirname ($path),
                ));
                $this->setResourceLoader ($aama);
            }
            return $this->_resourceLoader;
        }
    }
