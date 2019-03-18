<?php
    class Authentification_DeconnexionController extends App_Controller_Action {
        public function indexAction () {
            Zend_Auth::getInstance ()->clearIdentity ();
            $zsn = new Zend_Session_Namespace ();
            $zsn->unsetAll ();
            Zend_Session::destroy (true);
        }
    }