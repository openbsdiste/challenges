<?php
    class App_Model_Mapper_DbTable extends App_Model_Mapper_Abstract {
        public function save (App_Model_Entity $modele) {
            try {
                $donnees = $modele->getDonnees (true);
                foreach ($donnees as $k => $v) if (! is_null ($v) && is_array ($v)) $modele->$k = serialize ($v);
                if ($modele->cleEstNulle ()) {
                    if (sizeof ($modele->getCles ()) > 1) {
                        return $this->_getTable ()->insert ($modele->getDonnees (true));
                    } else {
                        return $this->_getTable ()->insert ($modele->getDonnees (false));
                    }
                }
                else {
                    if ($modele->getAutoincrement ()) {
                        return $this->_getTable ()->update ($modele->getDonnees (true), $modele->getQuotedCles ());
                    } else {
//print_r ($modele->getCles ());
                        $trouve = $this->find ($modele->getCles ());
                        if ($trouve) {
                            return $this->_getTable ()->update ($modele->getDonnees (true), $modele->getQuotedCles ());
                        } else {
                            return $this->_getTable ()->insert ($modele->getDonnees (true));
                        }
                    }
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function delete ($tableau) {
            try {
                $db = $this->_getTable ()->getAdapter ();
                //$tableau = (array) $tableau;
                $liste = array ();
                foreach ($tableau as $k => $v) {
                    if (is_array ($v)) $v = serialize ($v);
                    $liste [] = $db->quoteInto ("$k=?", $v);
                }
                return $this->_getTable ()->delete ($liste);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
