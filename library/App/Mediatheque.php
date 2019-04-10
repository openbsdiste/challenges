<?php
        /**
         * define.baseurl
         * 
         * define.js.name.src
         * define.js.name.type
         * define.js.name.attrs[]
         * define.js.name.depends[]
         * 
         * define.css.name.href
         * define.css.name.media
         * define.css.name.conditionalStylesheet
         * define.css.name.extras[]
         * define.css.name.depends[]
         * 
         * define.meta.name.depends[]
         * 
         * globals[]
         * 
         * specifics.module[]
         * specifics.module_controller[]
         * specifics.module_controller_action[]
         */

    class App_Mediatheque {
        protected $_profil;
        protected $_language;
        protected $_options;
        protected $_moduleName;
        protected $_controllerName;
        protected $_actionName;
        protected $_toParse;
        protected $_view;
        protected $_version;
                
        protected function _adapt ($string) {
            $motifs = array (
                '%language%', 
                '%profil%', 
                '%env%',
                '%module%', 
                '%controller%', 
                '%action%'
            );
            $remplace = array (
                $this->_language,
                $this->_profil,
                APPLICATION_ENV,
                $this->_moduleName,
                $this->_controllerName,
                $this->_actionName
            );
            return str_ireplace ($motifs, $remplace, $string);
        }
        
        protected function _normalizeScripts () {
            foreach ($this->_options ['define']['js'] as $k => $v) {
                if (is_string ($v)) {
                    $v = array ('src' => $v);
                    $this->_options ['define']['js'][$k] = $v;
                }
                if (! isset ($v ['src'])) unset ($this->_options ['define']['js'][$k]);
                else {
                    $this->_options ['define']['js'][$k]['src'] = $this->_adapt ($this->_options ['define']['js'][$k]['src']);
                    if (! isset ($v ['type']) || ! is_string ($v ['type'])) {
                        $this->_options ['define']['js'][$k]['type'] = 'text/javascript';
                    }
                    if (! isset ($v ['attrs']) || ! is_array ($v ['attrs'])) {
                        $this->_options ['define']['js'][$k]['attrs'] = array ();
                    }
                    if (! isset ($v ['depends'])) $this->_options ['define']['js'][$k]['depends'] = array ();
                    elseif (is_string ($v ['depends'])) $this->_options ['define']['js'][$k]['depends'] = array ($v ['depends']);
                }
            }
        }
        
        protected function _normalizeStyles () {
            foreach ($this->_options ['define']['css'] as $k => $v) {
                if (is_string ($v)) {
                    $v = array ('href' => $v);
                    $this->_options ['define']['css'][$k] = $v;
                }
                if (! isset ($v ['href'])) unset ($this->_options ['define']['css'][$k]);
                else {
                    $this->_options ['define']['css'][$k]['href'] = $this->_adapt ($this->_options ['define']['css'][$k]['href']);
                    if (! isset ($v ['media']) || ! is_string ($v ['media'])) {
                        $this->_options ['define']['css'][$k]['media'] = 'screen';
                    }
                    if (! isset ($v ['conditionalStylesheet']) || ! is_string ($v ['conditionalStylesheet'])) {
                        $this->_options ['define']['css'][$k]['conditionalStylesheet'] = '';
                    }
                    if (! isset ($v ['extras']) || ! is_array ($v ['extras'])) {
                        $this->_options ['define']['css'][$k]['extras'] = array ();
                    }
                    if (! isset ($v ['depends'])) $this->_options ['define']['css'][$k]['depends'] = array ();
                    elseif (is_string ($v ['depends'])) $this->_options ['define']['css'][$k]['depends'] = array ($v ['depends']);
                }
            }
        }
        
        protected function _normalizeMetas () {
            foreach ($this->_options ['define']['meta'] as $k => $v) {
                if (is_string ($v)) $this->_options ['define']['meta'][$k] = array ($v);
            }
        }
        
        protected function _normalize () {
            if (! isset ($this->_options ['baseurl'])) $this->_options ['baseurl'] = '';
            $this->_options ['baseurl'] = $this->_adapt ($this->_options ['baseurl']);
            if (! isset ($this->_options ['globals'])) $this->_options ['globals'] = array ();
            if (! isset ($this->_options ['specifics'])) $this->_options ['specifics'] = array ();
            if (! isset ($this->_options ['define'])) $this->_options ['define'] = array ();
            if (! isset ($this->_options ['define']['js'])) $this->_options ['define']['js'] = array ();
            if (! isset ($this->_options ['define']['css'])) $this->_options ['define']['css'] = array ();
            if (! isset ($this->_options ['define']['meta'])) $this->_options ['define']['meta'] = array ();
            $this->_normalizeScripts ();
            $this->_normalizeStyles ();
            $this->_normalizeMetas ();
        }
        
        protected function _addDependances (&$styles, &$scripts, $name) {
            list ($type, $key) = explode ('_', $name, 2);

            if (isset ($this->_options ['define'][$type][$key])) {
                foreach ($this->_options ['define'][$type][$key]['depends'] as $depends) {
                    $this->_addDependances ($styles, $scripts, $depends);
                }
                if ($type == 'css') {
                    $styles [$name] = $this->_options ['define']['css'][$key];
                    unset ($styles [$name]['depends']);
                }
                elseif ($type == 'js') {
                    $scripts [$name] = $this->_options ['define']['js'][$key];
                    unset ($scripts [$name]['depends']);
                }
            }
        }
        
        protected function _getElements () {
            $styles = array ();
            $scripts = array ();
            
            if (! isset ($this->_options ['globals'])) $this->_options ['globals'] = array ();
            elseif (is_string ($this->_options ['globals'])) $this->_options ['globals'] = array ($this->_options ['globals']);
            
            foreach ($this->_options ['globals'] as $global) $this->_addDependances ($styles, $scripts, $global);

            $specifics = $this->_options ['specifics'];
            if (isset ($specifics [$this->_moduleName])) {
                $elements = $specifics [$this->_moduleName];
                if (! is_array ($elements)) $elements = array ($elements);
                foreach ($elements as $element) $this->_addDependances ($styles, $scripts, $element);
            }
            if (isset ($specifics [$this->_moduleName . '_' . $this->_controllerName])) {
                $elements = $specifics [$this->_moduleName . '_' . $this->_controllerName];
                if (! is_array ($elements)) $elements = array ($elements);
                foreach ($elements as $element) $this->_addDependances ($styles, $scripts, $element);
            }
            if (isset ($specifics [$this->_moduleName . '_' . $this->_controllerName . '_' . $this->_actionName])) {
                $elements = $specifics [$this->_moduleName . '_' . $this->_controllerName . '_' . $this->_actionName];
                if (! is_array ($elements)) $elements = array ($elements);
                foreach ($elements as $element) $this->_addDependances ($styles, $scripts, $element);
            }
            return array ($styles, $scripts);
        }
        
        protected function _getBase () {
            $base = $this->_options ['baseurl'];
            if (strlen ($base) && (substr ($base, -1) != '/')) $base .= '/';
            return $base;
        }
        
        protected function _formatScripts ($scripts) {
            $liste = array ();
            $base = $this->_getBase ();
            foreach ($scripts as $script) {
                if (! is_array ($script ['src']) && ($script ['src'] != 'meta') && is_file (PUBLIC_PATH . '/' . $base . $script ['src'])) {
                    $liste [] = array (
                        $base . $script ['src'] . '?v=' . $this->_version,
                        $script ['type'],
                        $script ['attrs']
                    );
                } elseif (is_array ($script ['src'])) {
                    foreach ($script ['src'] as $src) {
                        if (is_file (PUBLIC_PATH . '/' . $base . $src)) {
                            $liste [] = array (
                                $base . $src . '?v=' . $this->_version,
                                $script ['type'],
                                $script ['attrs']
                            );
                        }
                    }
                }
            }
            return $liste;
        }
        
        protected function _formatStyles ($styles) {
            $liste = array ();
            $base = $this->_getBase ();
            foreach ($styles as $style) {
                if (! is_array ($style ['href']) && ($style ['href'] != 'meta') && is_file (PUBLIC_PATH . '/' . $base . $style ['href'])) {
                    $liste [] = array (
                        $base . $style ['href'] . '?v=' . $this->_version,
                        $style ['media'],
                        $style ['conditionalStylesheet'],
                        $style ['extras']
                    );
                } elseif (is_array ($style ['href'])) {
                    foreach ($style ['href'] as $href) {
                        if (is_file (PUBLIC_PATH . '/' . $base . $href)) {
                            $liste [] = array (
                                $base . $href . '?v=' . $this->_version,
                                $style ['media'],
                                $style ['conditionalStylesheet'],
                                $style ['extras']
                            );
                        }
                    }
                }
            }
            return $liste;
        }
        
        public function __construct (Zend_Controller_Request_Abstract $request) {
            $fc = Zend_Controller_Front::getInstance ();
            $bs = $fc->getParam ('bootstrap');

            $this->_version = $bs->getOption ('version');
            $this->_options = $bs->getOption ('mediatheque');
            $restOptions = $bs->getOption ('rest');
            if (! isset ($restOptions ['responders']) || ! is_array ($restOptions ['responders'])) {
                $restOptions ['responders'] = array ();
            }
            if ($this->_options && ! in_array ($this->_moduleName, $restOptions ['responders'])) {
                $this->_toParse = true;
                
                $this->_profil = App_Profil::get ();
                $this->_language = $bs->getResource ('locale')->getLanguage ();
                $this->_moduleName = $request->getModuleName ();
                $this->_controllerName = $request->getControllerName ();
                $this->_actionName = $request->getActionName ();

                $vr = Zend_Controller_Action_HelperBroker::getStaticHelper ('viewRenderer');
                if ($vr->view == null) {
                    $vr->initView ();
                }
                $this->_view = $vr->view;
                
                $jquery = $this->_view->jQuery ();
                $jquery->setRenderMode (ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
            } else {
                $this->_toParse = false;
            }
        }
        
        public function parse () {
            if ($this->_toParse) {
                $cacheEMCA = implode ("_", array (APPLICATION_ENV, $this->_moduleName, $this->_controllerName, $this->_actionName));
                $cacheName = "media_" . md5 ($cacheEMCA);
                $cache = App_Cache::getCache ();
                $listeStyles = $cache->load ($cacheName . "_styles");
                $listeScripts = $cache->load ($cacheName . "_scripts");
                
                if (! $listeStyles || ! $listeScripts) {
                    $this->_normalize ();
                    list ($styles, $scripts) = $this->_getElements ();
                    $listeScripts = $this->_formatScripts ($scripts);
                    $listeStyles = $this->_formatStyles ($styles);
                    $cache->save ($listeStyles, $cacheName . "_styles");
                    $cache->save ($listeScripts, $cacheName . "_scripts");
                }
                
                foreach ($listeScripts as $script) {
                    $this->_view->headScript()->appendFile (
                            $script [0],
                            $script [1],
                            $script [2]
                    );
                }
                foreach ($listeStyles as $style) {
                    $this->_view->headLink ()->appendStylesheet (
                        $style [0],
                        $style [1],
                        $style [2],
                        $style [3]
                    );
                }
            }
        }
    }