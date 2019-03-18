<?php
    class App_Controller_Plugin_Mediatheque extends Zend_Controller_Plugin_Abstract {
        public function preDispatch (Zend_Controller_Request_Abstract $request) {
            parent::preDispatch ($request);
            
            $mediatheque = new App_Mediatheque ($request);
            $mediatheque->parse ();
        }
    }