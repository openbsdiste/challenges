<?php
    class App_View_Helper_Version extends Zend_View_Helper_Abstract {
        public function version () {
            $fc = Zend_Controller_Front::getInstance ();
            $bs = $fc->getParam ('bootstrap');
            return $bs->getOption ('version');
        }
    }