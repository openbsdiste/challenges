<?php
	class App_Controller_Plugin_FilterAccess extends Zend_Controller_Plugin_Abstract {
		private $_acl = null;
		private $_auth = null;
		
		public function __construct (Zend_Acl $acl, Zend_Auth $auth) {
			$this->_acl = $acl;
			$this->_auth = $auth;
		}
		
	    public function preDispatch (Zend_Controller_Request_Abstract $request) {
                $module = $this->_request->getModuleName ();
                $controleur = $this->_request->getControllerName ();
                $action = $this->_request->getActionName ();
                $ressource = $module . '_' . $controleur;

                $role = "guest";
                $identite = $this->_auth->getStorage ()->read ();
                if (! is_null ($identite)) {
                    $role = $identite->role;
                }

                Zend_View_Helper_Navigation::setDefaultAcl ($this->_acl);
                Zend_View_Helper_Navigation::setDefaultRole ($role);

                if (! $this->_acl->isAllowed ($role, $ressource, $action) && is_null ($this->_request->getParam ('error_handler'))) {
                    $request
                        ->setModuleName ('authentification')
                        ->setControllerName ('connexion')
                        ->setActionName ('index');
                    if ($role != "guest") {
                        $request->setControllerName ('droits');
                    }
                }
	    }
	}