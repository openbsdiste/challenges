<?php
    class App_Profil {
        public static function get () {
            $profil = 'guest';
            $auth = Zend_Auth::getInstance ();
            if ($auth->hasIdentity ()) $profil = $auth->getIdentity ()->role;
            return $profil;
        }
    }