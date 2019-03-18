<?php
    class Authentification_ConnexionController extends App_Controller_Action {
        private function _getAuthAdapter () {
        	$authAdapter = new Zend_Auth_Adapter_DbTable (Zend_Db_Table::getDefaultAdapter ());
        	$authAdapter
        	   ->setTableName ('utilisateurs')
        	   ->setIdentityColumn ('login')
        	   ->setCredentialColumn ('password')
        	   ->setCredentialTreatment ('MD5(?) AND actif=1');
            return $authAdapter;
        }
		
        public function indexAction () {
            $authAdapter = $this->_getAuthAdapter ();
            $formulaire = new Authentification_Form_Auth ();
            
            if ($this->_request->isPost ()) {
                if ($formulaire->isValid ($this->_getAllParams ())) {
                    
                    $username = $formulaire->getValue ('login');
                    $password = $formulaire->getValue ('password');
                    
                    $authAdapter
                        ->setIdentity ($username)
                        ->setCredential ($password);
                    
                    $auth = Zend_Auth::getInstance ();
                    if ($auth->authenticate ($authAdapter)->isValid ()) {
                        $authStorage = $auth->getStorage ();
                        $res = $authAdapter->getResultRowObject ();
//                        $res = $authAdapter->getResultRowObject (null, array ('password'));
                        $res->cle = sha1 ($password);
                        $authStorage->write ($res);
                        $this->_redirect ();
                    } else {
                        $this->view->message = __('auth.erreur');
                    }
                }
            }
            $this->view->formulaire = $formulaire;
        }
    }