<?php
    class App_Localization {
        protected static $_instance = null;
        protected $_translation = null;
        
        protected function __construct () {
        }
        
        public static function getInstance () {
            if (self::$_instance === null) {
                self::$_instance = new self ();
            }
            return self::$_instance;
        }
        
        public function setTranslation ($resourceTranslation) {
            if ($this->_translation === null) {
                $this->_translation = $resourceTranslation;
            }
        }
        
        public function localize ($message, $locale = null) {
            return ($this->_translation === null) ? $message : $this->_translation->translate ($message, $locale);
        }
    }
    
    function __ ($message, $locale = null) {
        return App_Localization::getInstance ()->localize ($message, $locale);
    }