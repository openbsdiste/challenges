<?php
    class App_Acl extends Zend_Acl {
        protected function _defRoles ($roles) {
            foreach ($roles as $role => $parents) {
                if ($parents == '') $parents = null;
                if ($parents != null) $parents = explode (',', $parents);
                $this->addRole (new Zend_Acl_Role ($role), $parents);
            }
        }
        
        protected function _defRessources ($ressources) {
            foreach ($ressources as $ressource => $parents) {
                if (empty ($parents)) $parents = null;
                elseif (strpos (',', $parents)) {
                    throw new Zend_Acl_Exception ('Configuration des ACLs incorecte');
                }
                $this->addResource (new Zend_Acl_Resource ($ressource), $parents);
            }
        }

        protected function _defRessourcesAuto () {
            $fc = Zend_Controller_Front::getInstance ();
            foreach ($fc->getControllerDirectory() as $module => $path) {
            	$moduleName = strtolower ($module);
                $this->addResource (new Zend_Acl_Resource ($moduleName));
            	foreach (scandir ($path) as $file) {
            		if (strstr ($file, "Controller.php") !== false) {
            			$controleurName = strtolower (substr ($file, 0, strlen ($file) - 14));
            			$this->addResource (new Zend_Acl_Resource ($moduleName . '_' . $controleurName), $moduleName);
            		}
            	}
            }
        }

        protected function _defRegles ($role, $regles) {
            foreach ($regles as $tmc => $ca) {
                if (strpos ($tmc, '_')) {
                    $tmc = explode ('_', $tmc);
                    $type = strtolower (array_shift ($tmc));
                    $mc = strtolower (implode ('_', $tmc));
                } else {
                    $type = strtolower ($tmc);
                    $mc = null;
                }
                if (is_null ($ca) || empty ($ca) || ($ca == "*")) $ca = null;
                else {
                	$ca = explode (',', $ca);
                }
                $this->$type ($role, $mc, $ca);
            }
        }
    	
    	public function __construct ($configuration) {
            $this->_defRoles ($configuration ['roles']);
            $this->_defRessourcesAuto ();
            if (isset ($configuration ['ressources'])) {
            	$this->_defRessources ($configuration ['ressources']);
            }
            foreach (array_keys ($configuration ['roles']) as $role) {
                if (isset ($configuration [$role])) $this->_defRegles ($role, $configuration [$role]);
            }
    	}
    }