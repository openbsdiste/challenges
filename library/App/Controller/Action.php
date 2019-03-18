<?php
    class App_Controller_Action extends Zend_Controller_Action {
        protected $_role = null;
        protected $_identity = null;
        
        public function init () {
            $this->_role = App_Profil::get ();
            
            if ($this->_role != Model_Enum_Roles::GUEST) {
                $this->_identity = Zend_Auth::getInstance ()->getIdentity ();
                $this->view->nomClub = Zend_Auth::getInstance ()->getIdentity ()->club;
            } else {
                $this->view->nomClub = '';
            }
            $this->view->role = $this->_role;
        }
        
        public function disableLayout () {
            $this->_helper->layout->disableLayout ();
        }
    }