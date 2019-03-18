<?php
    class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
        public function getResourceLoader () {
            if ((null === $this->_resourceLoader) && (false !== ($namespace = $this->getAppNamespace ()))) {
                $r = new ReflectionClass ($this);
                $path = $r->getFileName ();
                $this->setResourceLoader (new App_Application_Module_Autoloader (array (
                    'namespace' => $namespace,
                    'basePath'  => dirname ($path),
                )));
            }
            return $this->_resourceLoader;
        }
        
        protected function _initAutoload () {
            return Zend_Loader_Autoloader::getInstance ();
        }
        
        protected function _initModuleAutoload () {
            $this->bootstrap ('autoload');
            $module = new App_Application_Module_Autoloader (
                array (
                    'namespace' => '',
                    'basePath'  => APPLICATION_PATH . '/modules/default'
                )
            );
            return $module;
        }
        
        public function _initDbCache () {
            $this->bootstrap ('moduleAutoload');
            $cache = App_Cache::getCache ();
            Zend_Db_Table::setDefaultMetadataCache ($cache);
        }

        public function _initTranslate () {
            if (! $this->getOption ('locale')) {
                $this->setOptions (array ('resources' => array ('locale' => array ('default' => 'fr_FR'))));
            }
            $this->bootstrap ('locale');
            $locale = $this->getResource ('locale');
            $langage = $locale->getLanguage ();
            $translate = new Zend_Translate (
                'ini',
                APPLICATION_PATH . '/configuration/languages',
                'auto',
                array ('scan' => Zend_Translate::LOCALE_DIRECTORY)
            );
            if (file_exists (APPLICATION_PATH . '/configuration/languages/' . $langage . '.ini')) {
                $translate->getAdapter ()->addTranslation (
                    array(
                        'content' => APPLICATION_PATH . '/configuration/languages/' . $langage . '.ini',
                        'locale' => $langage
                    )
                );
            }
            if (file_exists (APPLICATION_PATH . '/configuration/languages/fr.ini')) {
                $translate->getAdapter ()->addTranslation (
                    array(
                        'content' => APPLICATION_PATH . '/configuration/languages/fr.ini',
                        'locale' => 'fr'
                    )
                );
            }
            if ($translate->isAvailable ($langage)) {
                $translate->setLocale ($langage);
            } else {
                $translate->setLocale ('fr');
            }
            $translateZV = new Zend_Translate (array (
                'adapter' => 'array',
                'content' => LIBRARY_PATH . '/resources/languages/' . $langage . '/Zend_Validate.php',
                'locale' => $locale
            ));
            $translate->addTranslation ($translateZV);
            
            if (APPLICATION_ENV != 'production') {
                $writer = new Zend_Log_Writer_Stream (DATA_PATH . '/logs/translate.log');
                $log = new Zend_Log ($writer);
                $translate->setOptions (array (
                    'log' => $log,
                    'logUntranslated' => true,
                    'logMessage' => "Le message '%message%' est indefini pour la langue '%locale%'",
                    'route' => array ($translate->getLocale () => 'fr')
                ));
            }
            
            Zend_Registry::set ('Zend_Translate', $translate);
            App_Localization::getInstance ()->setTranslation ($translate);
            //Zend_Validate_Abstract::setDefaultTranslator ($translate);

            return $translate;
        }
    
        public function _initRestful () {
            $fc = Zend_Controller_Front::getInstance ();
            $fc->setRequest (new App_Controller_Request_Rest ());
            $fc->setResponse (new App_Controller_Response_Rest ());
            $options = $this->getOption ('rest');
            if (isset ($options ['responders']) && is_array ($options ['responders'])) {
                $responders = $options ['responders'];
            } else {
                $responders = array ();
            }
            $route = new Zend_Rest_Route ($fc, array (), $responders);
            $fc->getRouter ()->addRoute ('restful', $route);
        }

        protected function _initZFDebug () {
            if ($this->hasOption ('zfdebug') && (APPLICATION_ENV != 'production')) {
                $autoloader = Zend_Loader_Autoloader::getInstance ();
                $autoloader->registerNamespace ('ZFDebug');
                $this->bootstrap ('FrontController');
                $front = $this->getResource ('FrontController');
                $options = $this->getOption ('zfdebug');
                if (isset ($options ['plugins']['Database'])) {
                    if ($this->hasPluginResource ('db')) {
                        $this->bootstrap ('db');
                        $db = $this->getPluginResource ('db')->getDbAdapter ();
                        $options ['plugins']['Database'] = array ('adapter' => $db);
                    } else {
                        unset ($options ['plugins']['Database']);
                    }
                }
                if (isset ($options ['plugins']['Cache'])) {
                    if ($this->hasPluginResource ('cache')) {
                        $this->bootstrap ('cache');
                        $cache = $this-getPluginResource ('cache')->getDbAdapter ();
                        $options ['plugins']['Cache'] = array ('backend' => $cache->getBackend ());
                    } else {
                        unset ($options ['plugins']['Cache']);
                    }
                }
                $zfdebug = new App_Controller_Plugin_Debug ($options);
                $front->registerPlugin ($zfdebug);
            }
        }
    }