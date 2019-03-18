<?php
    class App_Application_Resource_Navigation extends Zend_Application_Resource_Navigation {
    	/* FL
        private function _autoResourcePrivilege ($menu) {
            if (is_array ($menu)) {
                if (isset ($menu ['module']) && isset ($menu ['controller']) && isset ($menu ['action'])) {
            	    if (! isset ($menu ['resource']) && ! isset ($menu ['privilege'])) {
            		    $menu ['resource'] = $menu ['module'] . '_' . $menu ['controller'];
            		    $menu ['privilege'] = $menu ['action'];
            	    }
                }
                foreach ($menu as $k => $m) $menu [$k] = $this->_autoResourcePrivilege ($m);
            }
            return $menu;
    	}
        */
    	private function _autoResourcePrivilege ($config) {
            $menu = array ();
            foreach ($config as $k => $m) {
                if (isset ($m ['module']) && isset ($m ['controller']) && isset ($m ['action']) && ! isset ($m ['resource']) && ! isset ($m ['privilege'])) {
                    $m ['resource'] = $m ['module'] . '_' . $m ['controller'];
                    $m ['privilege'] = $m ['action'];
                } elseif (isset ($m ['uri']) && ! isset ($m ['resource']) && ! isset ($m ['privilege'])) {
                    $m ['resource'] = 'default_index';
                    $m ['privilege'] = 'index';
                }
                $menu [$k] = $m;
            }
            return $menu;
    	}

        public function init () {
        	$options = $this->getOptions ();
        	$config = App_LoadConfig::file ($options ['config']);
            if (is_array ($config)) {
                $menu = $this->_autoResourcePrivilege ($config);
            }
            $this->_container = new Zend_Navigation ($menu);
            $this->store ();
            return $this->_container;
        }
    }