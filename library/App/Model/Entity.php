<?php
    class App_Model_Entity {
        private $_champs = array ();
        protected $_cles = array ('id');
        protected $_autoincrement = true;

        public function __construct ($donnees = array ()) {
            if (is_null ($this->_champs) || empty ($this->_champs)) {
                $vars = get_object_vars ($this);
                foreach (array_keys ($vars) as $champ) {
                    $premier = substr ($champ, 0, 1);
                    if (($premier >= 'a') && ($premier <= 'z')) $this->_champs [] = $champ;
                    $this->setDonnees ($donnees);
                }
            }
        }
        
        public function __set ($cle, $valeur) {
            if (! in_array ($cle, $this->_champs)) throw new Zend_Db_Exception ("Appel setter incorrect, champ innexistant");
            $methode = "set" . ucfirst ($cle);
            $this->$methode ($valeur);
        }
        
        public function __get ($cle) {
            if (! in_array ($cle, $this->_champs)) throw new Zend_Db_Exception ("Appel getter incorrect, champ innexistant");
            $methode = "get" . ucfirst ($cle);
            return $this->$methode ();
        }
        
        public function set ($cle, $valeur) {
            $this->__set ($cle, $valeur);
        }
        
        public function get ($cle) {
            return $this->__get ($cle);
        }
        
        public function getDonnees ($avecCles = false) {
            $donnees = array ();
            foreach ($this->_champs as $c) {
                if ($avecCles || ! in_array ($c, $this->_cles)) {
                    $donnees [$c] = $this->$c;
                }
            }
            return $donnees;
        }
        
        public function setDonnees ($donnees) {
            if (! is_array ($donnees)) throw new Zend_Db_Exception ("Appel incorrect setDonnees sur le modèle");
            foreach ($donnees as $cle => $valeur) {
                if (in_array ($cle, $this->_champs)) $this->$cle = $valeur;
            }
        }
        
        public function getCles () {
            $cles = array ();
            foreach ($this->_cles as $c) {
                $cles [$c] = $this->$c;
            }
            return $cles;
        }
        
        public function getAutoincrement () {
            return $this->_autoincrement;
        }
        
        public function getQuotedCles () {
            $db = Zend_Db_Table::getDefaultAdapter ();
            $cles = array ();
            foreach ($this->_cles as $c) {
                $cles [] = $db->quoteInto ("$c = ?", $this->$c);
            }
            return implode (" AND ", $cles);
        }
        
        public function cleEstNulle () {
            if (sizeof ($this->_cles) == sizeof ($this->_champs)) return true;
            foreach ($this->_cles as $cle) if ($this->$cle === null) return true;
            return false;
        }
        
        public function __call ($methode, $parametres) {
            $champ = strtolower (substr ($methode, 3, 1)) . substr ($methode, 4);
            if ((sizeof ($parametres) == 0) && (substr ($methode, 0, 3) == "get")) {
                return $this->$champ;
            } elseif ((sizeof ($parametres) == 1) && (substr ($methode, 0, 3) == "set")) {
                $this->$champ = $parametres [0];
            } else throw new Zend_Db_Exception ("Méthode invalide : $methode");
        }
    }