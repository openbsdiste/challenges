<?php
    class App_Controller_Challenge extends App_Controller_Action {
        protected $_chalInfos = false;
        protected $_metierChallenge = null;
        
        public function init () {
            parent::init ();
            $zsn = new Zend_Session_Namespace ('challenge');
            if (isset ($zsn->chalId) && ($zsn->chalId)) {
                $this->_metierChallenge = new Model_Metier_Challenges ();
                $this->_chalInfos = $this->_metierChallenge->getChallenge ($zsn->chalId);
                if ($this->_chalInfos === false) {
                    $this->redirect ();
                }
            } else {
                $this->redirect ();
            }
        }
    }