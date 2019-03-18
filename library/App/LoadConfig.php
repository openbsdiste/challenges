<?php
    class App_LoadConfig {
        public static function file ($fichier, $env = APPLICATION_ENV) {
            if (! file_exists ($fichier) || ! is_file ($fichier)) {
                throw new Zend_Config_Exception ('Configuration invalide : ' . $fichier);
            }
            $extension = ucfirst (substr ($fichier, sizeof ($fichier) - 4));
            $configurateur = 'Zend_Config_' . $extension;
            $configuration = new $configurateur ($fichier, $env);
            return $configuration->toArray ();
        }
    }