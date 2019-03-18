<?php
    require_once 'Zend/Application.php';
    require_once 'App/Cache.php';
    
    class App_Application extends Zend_Application {
        
        protected function _loadConfig ($file) {
            $cacheName = "config_" . APPLICATION_ENV;
            $cache = App_Cache::getCache ();
            $config = $cache->load ($cacheName);
            if (! $config) {
                $config = parent::_loadConfig ($file);
                $configDir = realpath (dirname ($file) . '/config.d');
                if (is_dir ($configDir)) {
                    $dir = new DirectoryIterator ($configDir);
                    foreach ($dir as $fileinfo) {
                        $filename = $fileinfo->getFilename ();
                        $suffixRaw = pathinfo ($filename, PATHINFO_EXTENSION);
                        $suffix = ($suffixRaw === 'dist')
                            ? pathinfo (basename ($filename, ".$suffixRaw"), PATHINFO_EXTENSION)
                            : $suffixRaw;
                        if (in_array (strtolower ($suffix), array ('ini', 'xml', 'json', 'yml', 'yaml', 'php', 'inc'))) {
                            $subconfig = parent::_loadConfig ($configDir . '/' . $filename);
                            $config = array_merge_recursive ($config, $subconfig);
                        }
                    }
                }
                $cache->save ($config, $cacheName);
            }
            return $config;
        }
    }