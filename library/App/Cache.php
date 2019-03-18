<?php
    class App_Cache {
        protected static $_cache = null;
        
        public final function __construct () {
            throw new Exception ('App_Cache is static !');
        }
        
        public static function getCache () {
            if (self::$_cache === null) {
                $frontend = array (
                    'lifetime' => 0,
                    'automatic_serialization' => true,
                    'caching' => (APPLICATION_ENV != 'development')
                );
                $backend = array (
                    'cache_dir' => DATA_PATH . '/cache'
                );
                self::$_cache = Zend_Cache::factory ('Core', 'File', $frontend, $backend);
            }
            return self::$_cache;
        }
    }