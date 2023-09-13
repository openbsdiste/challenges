<?php
    if(! ini_get ('safe_mode')) {
        set_time_limit (0);
    }
    defined ('PUBLIC_PATH') || define ('PUBLIC_PATH', realpath (dirname (__FILE__)));
    defined ('APPLICATION_ENV') || define ('APPLICATION_ENV', (getenv ('APPLICATION_ENV') ? getenv ('APPLICATION_ENV') : 'development'));

    if (APPLICATION_ENV == 'production') {
        defined ('BACKEND_PATH') || define ('BACKEND_PATH', realpath (PUBLIC_PATH . '/../../hors_sites/challenges'));
    } else {
        defined ('BACKEND_PATH') || define ('BACKEND_PATH', realpath (PUBLIC_PATH . '/../..'));
    }
    defined ('DATA_PATH') || define ('DATA_PATH', realpath (BACKEND_PATH . '/data'));
    defined ('APPLICATION_PATH') || define ('APPLICATION_PATH', realpath (BACKEND_PATH . '/application'));
    defined ('LIBRARY_PATH') || define ('LIBRARY_PATH', realpath (BACKEND_PATH . '/library'));

    # https://github.com/Shardj/zf1-future (ZF version compatible php 8.1)
    defined ('ZEND_PATH') || define ('ZEND_PATH', realpath (BACKEND_PATH . '/../.Zend/1.22.0/'));

    set_include_path (LIBRARY_PATH . PATH_SEPARATOR . ZEND_PATH);

    try {
        require_once 'App/Application.php';

        $app = new App_Application (APPLICATION_ENV, APPLICATION_PATH . '/configuration/application.ini');
        Zend_Session::start ();
        $app->bootstrap ()
            ->run ();
    } catch (Exception $e) {
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\n";
        echo "<html><head><title>Erreur</title></head><body>\n";
        echo "<h1>Erreur technique. Impossible de poursuivre</h1>\n";
        echo '<h2>Type d\'exception : ' . get_class ($e) . "</h2>\n";
        echo '<h2>Message : ' . $e->getMessage () . "</h2>\n";
        if (APPLICATION_ENV != 'production') {
            echo "<pre>\n" . $e->getTraceAsString () . "\n</pre>\n";
        }
        echo "</body></html>\n";
    }
