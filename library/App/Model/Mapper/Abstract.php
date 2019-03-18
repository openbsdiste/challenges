<?php
    abstract class App_Model_Mapper_Abstract {
        protected $_tableName;
        protected $_modelName;
        protected $_options;
        protected $_table = null;

        protected function _getTable () {
            try {
                if ($this->_table === null) $this->_table = new $this->_tableName ($this->_options);
            } catch (Exception $e) {
                throw $e;
            }
            return $this->_table;
        }

        public function __construct ($options = array ()) {
            try {
                $classe = get_class ($this);
                $this->_modelName = str_replace ('_Mapper_', '_', $classe);
                $this->_options = $options;
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        public function getModelName () {
            return $this->_modelName;
        }

        public function find ($cles) {
            try {
                if (func_num_args () > 1) $cles = func_get_args ();
                if (is_array ($cles)) {
                    $resultat = call_user_func_array (array ($this->_getTable (), "find"), $cles);
                } else {
                    $resultat = $this->_getTable ()->find ($cles);
                }
                if (is_null ($resultat) || ! count ($resultat)) $retour = false;
                else {
                    $enreg = $resultat->current ();
                    $retour = new $this->_modelName ();
                    foreach ($enreg as $champ => $valeur) {
                        //if (@unserialize ($valeur) !== false) $valeur = unserialize ($valeur);
                        $retour->$champ = $valeur; 
                    }
                }
            } catch (Exception $e) {
                throw $e;
            }
            return $retour;
        }

        public function findArray ($array = array ()) {
            try {
                $db = $this->_getTable ()->getAdapter ();
                $select = $this->select ();
                foreach ($array as $key => $value) $select->where ($db->quoteInto ("$key = ?", $value));

                $resultSet = $this->fetchAll ($select);
            } catch (Exception $e) {
                throw $e;
            }
            return $resultSet;
        }

        public function fetchAll ($where = null, $order = null, $count = null, $offset = null) {
            try {
                $liste = $this->_getTable ()->fetchAll ($where, $order, $count, $offset);
                $retour = array ();
                foreach ($liste as $ligne) {
                    $modele = new $this->_modelName ();
                    foreach ($ligne as $cle => $valeur) {
                        //if (@unserialize ($valeur) !== false) $valeur = unserialize ($valeur);
                        $modele->$cle = $valeur;
                        
                    }
                    $retour [] = $modele;
                }
            } catch (Exception $e) {
                throw $e;
            }
            return $retour;
        }

        public function select ($withFromPart = Zend_Db_Table::SELECT_WITHOUT_FROM_PART) {
            return $this->_getTable ()->select ($withFromPart);
        }

        public function toArray ($data, $id = null) {
            $retour = array ();
            if (is_array ($data)) {
                foreach ($data as $v) {
                    if ($v instanceof App_Model_Entity) {
                        if (is_null ($id)) $retour [] = $v->getDonnees (true);
                        else $retour [$v->$id] = $v->getDonnees (true);
                    } else throw new Zend_Exception ("Appel incorrect à toArray");
                }
            } else $retour = $this->toArray (array ($data), $id);
            return $retour;
        }

        public function toArrayOne ($data, $id, $champ) {
            $retour = array ();
            if (is_array ($data)) {
                foreach ($data as $v) {
                    if ($v instanceof App_Model_Entity) {
                        $retour [$v->$id] = $v->$champ;
                    } else throw new Zend_Exception ("Appel incorrect à toArrayOne");
                }
            } elseif ($data instanceof App_Model_Entity) {
                $retour [$data->$id] = $data->$champ;
            } else throw new Zend_Exception ("Appel incorrect à toArray");
            return $retour;
        }
        
        public function toArrayId ($data, $id) {
            $retour = array ();
            if (is_array ($data)) {
                foreach ($data as $v) {
                    if ($v instanceof Zend_Model_Entity) {
                        $retour [] = $v->$id;
                    } //else throw new Zend_Exception ("Appel incorrect à toArrayId");
                }
            } elseif ($data instanceof App_Model_Entity) {
                $retour [] = $data->id;
            } else throw new Zend_Exception ("Appel incorrect à toArrayId");
            return $retour;
        }
        
        public function toAssocArray ($data, $champs) {
            $retour = array ();
            if (is_array ($data)) {
                foreach ($data as $v) {
                    if ($v instanceof App_Model_Entity) {
                        $ligne = array ();
                        foreach ($champs as $champ) $ligne [$champ] = $v->$champ;
                        $retour [] = $ligne;
                    } else throw new Zend_Exception ("Appel incorrect à toAssocArray");
                }
            } elseif ($data instanceof App_Model_Entity) {
                $retour = $this->toAssocArray (array ($data), $champs);
            } else throw new Zend_Exception ("Appel incorrect à toAssocArray");
            return $retour;
        }
        
        public function quoteInto ($text, $value, $type = null, $count = null) {
            $dba = Zend_Db_Table::getDefaultAdapter ();
            return $dba->quoteInto ($text, $value, $type, $count);
        }
        
        public function getTable () {
            return $this->_getTable ();
        }
        
        public function getCount ($select = null) {
            return $this->_getTable ()->getCount ($select);
        }
        
        abstract public function save (App_Model_Entity $modele);

        abstract public function delete ($tableau);

    }