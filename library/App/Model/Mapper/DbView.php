<?php
    class App_Model_Mapper_DbVue extends App_Model_Mapper_Abstract {
        public function save (App_Model_Entity $modele) {
            throw new Zend_Exception ("Impossible de sauver un enregistrement d'une vue");
        }

        public function delete ($tableau) {
            throw new Zend_Exception ("Impossible de supprimer un enregistrement d'une vue");
        }
    }