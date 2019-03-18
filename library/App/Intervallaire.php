<?php
    /**
     * left < right
     * si feuille : right - left = 1
     * right - left = 2 * nbre de fils + 1
     */
    class App_Intervallaire {
        /**
         * @var App_Model_Mapper_DbTable
         */
        protected $_mapper;
        
        /**
         * @var stdClass
         */
        protected $_fields;
        
        /**
         * Récupère la liste des champs de la table
         * @return array
         */
        protected function _getFieldsFromModel () {
            $modelClassName = $this->_mapper->getModelName ();
            $model = new $modelClassName ();
            $donnees = $model->getDonnees (true);
            $fields = array ();
            foreach ($donnees as $k => $v) $fields [$k] = $v;
            return $fields;
        }
        
        /**
         * Alimente le tableau des champs avec les paramètres
         * @param array $fields
         * @throws Exception
         */
        protected function _parseFields ($fields) {
            $model = $this->_getFieldsFromModel ();
            foreach ($fields as $key => $value) {
                if (isset ($model [$value])) {
                    $this->_fields->$key = $value;
                    unset ($fields [$key]);
                }
            }
            if (! empty ($fields)) throw new Exception ('champs inconnus présents...');
            return $this;
        }
        
        /**
         * Vérifie que le mapping est complet
         * @throws Exception
         */
        protected function _checkFields () {
            $model = $this->_getFieldsFromModel ();
            $rc = new ReflectionObject ($this->_fields);
            $props = $rc->getProperties ();
            foreach ($props as $prop) {
                $key = $prop->getName ();
                if (isset ($model [$this->_fields->$key])) {
                    unset ($model [$this->_fields->$key]);
                }
            }
            foreach ($model as $k => $v) $this->_fields->$k = $k;
            return $this;
        }

        protected function _getFieldsNames () {
            return array (
                $this->_fields->id,
                $this->_fields->left,
                $this->_fields->right,
                $this->_fields->level
            );
        }
        /**
         * @param App_Model_Mapper_DbTable $mapper
         * @param array $fields
         * @throws Exception
         */
        public function __construct (App_Model_Mapper_DbTable $mapper, $fields = array ()) {
            $this->_mapper = $mapper;

            $this->_fields = new stdClass ();
            $this->_fields->id = 'id';
            $this->_fields->left = 'left';
            $this->_fields->right = 'right';
            $this->_fields->level = 'level';
            
            try {
                $this->_parseFields ($fields)
                     ->_checkFields ();
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function getNode ($id) {
            try {
                $node = $this->_mapper->find ($id);
            } catch (Exception $e) {
                throw $e;
            }
            return $node;
        }
        
        /**
         * Réinitialise l'arbre
         * @return App_Intervallaire
         * @throws Exception
         */
        public function dropTree () {
            try {
                $table = $this->_mapper->getTable ();
                $name = $table->info (Zend_Db_Table::NAME);
                $dba = $table->getAdapter ();

                $dba->query ('TRUNCATE TABLE `' . $name . '`');
                
                $modelName = $this->_mapper->getModelName ();
                $tree = new $modelName ();
                $tree->setDonnees (array (
                    $this->_fields->left => 1,
                    $this->_fields->right => 4,
                    $this->_fields->level => 0
                ));
                $this->_mapper->save ($tree);
                $tree = new $modelName ();
                $tree->setDonnees (array (
                    $this->_fields->left => 2,
                    $this->_fields->right => 3,
                    $this->_fields->level => 1
                ));
                $this->_mapper->save ($tree);
            } catch (Exception $e) {
                throw $e;
            }
            return $this;
        }
        
        public function getChildren ($id, $recursive = false) {
            $children = array ();
            $node = $this->getNode ($id);
            if ($node) {
                list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
                $select = $this->_mapper->select ()
                    ->where ($this->_mapper->quoteInto ("`$left` > ?", $node->$left))
                    ->where ($this->_mapper->quoteInto ("`$right` < ?", $node->$right));
                if (! $recursive) {
                    $select->where ($this->_mapper->quoteInto ("`$level` = ?", ($node->$level + 1)));
                }
                $select->order (array ($level, $left));
                try {
                    $res = $this->_mapper->fetchAll ($select);
                } catch (Exception $e) {
                    throw $e;
                }
                foreach ($res as $element) $children [$element->$ident] = $element->getDonnees (true);
            }
            return $children;
        }

        public function getChildrenLeaves ($id) {
            $childrenLeaves = array ();
            $node = $this->getNode ($id);
            if ($node) {
                list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
                $select = $this->_mapper->select ()
                    ->where ($this->_mapper->quoteInto ("`$left` > ?",$node->$left))
                    ->where ($this->_mapper->quoteInto ("`$right` < ?" . $node->$right))
                    ->where ("`$level`=max(`$level`)");
                $select->order ("`$left` asc");
                try {
                    $res = $this->_mapper->fetchAll ($select);
                } catch (Exception $e) {
                    throw $e;
                }
                foreach ($res as $element) $childrenLeaves [$element->$ident] = $element->getDonnees (true);
            }
            return $childrenLeaves;
        }

        public function getParents ($id, $recursive = false) {
            $parents = array ();
            $node = $this->getNode ($id);
            if ($node) {
                list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
                $select = $this->_mapper->select ()
                    ->where ($this->_mapper->quoteInto ("`$left` < ?",$node->$left))
                    ->where ($this->_mapper->quoteInto ("`$right` > ?" . $node->$right));
                if (! $recursive) {
                    $select->where ($this->_mapper->quoteInto ("`$level` = ?", ($node->$level - 1)));
                }
                $select->order ("`$level` desc, `$left` asc");
                try {
                    $res = $this->_mapper->fetchAll ($select);
                } catch (Exception $e) {
                    throw $e;
                }
                foreach ($res as $element) $parents [$element->$ident] = $element->getDonnees (true);
            }
            return $parents;
        }
        
        public function countChildrenElements ($id) {
            $count = 0;
            $node = $this->getNode ($id);
            if ($node) {
                $left = $this->_fields->left;
                $right = $this->_fields->right;
                $count = ($node->$right - $node->$left - 1) / 2;
            }
            return $count;
        }
        
        /**
         * 
         * @param type $id
         * @return type
         * @todo Refaire avec count(*)
         */
        public function countParentsElements ($id) {
            return sizeof ($this->getParents ($id, true));
        }

        /**
         * 
         * @param type $id
         * @return type
         * @todo Refaire avec count(*)
         */
        public function countChildrenLeaves ($id) {
            return sizeof ($this->getChildrenLeaves ($id));
        }
        
        /**
         * 
         * @param type $id
         * @todo Refaire avec count(*)
         */
        public function countSameLevelElements ($id) {
            $count = 0;
            $parents = $this->getParents ($id);
            if (! empty ($parents)) {
                $parentId = $parents [0][$this->_fields->id];
                $count = sizeof ($this->getChildren ($parentId));
            } elseif ($this->getNode ($id)) $count = 1;
            return $count;
        }
        
        public function createRightLeaf ($refId, $node) {
            if (! is_array ($node)) throw new Exception ('Invalid node given');
            if (isset ($node [$this->_fields->id])) unset ($node [$this->_fields->id]);
            if (! isset ($node [$this->_fields->level]) || ! $node [$this->_fields->level]) {
                throw new Exception ('Invalid level specified');
            }
            $rNode = $this->getNode ($refId);
            if (! $rNode) throw new Exception ('Invalid reference node given');
            list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
            
            $pNodes = $this->getParents ($rNode [$ident]);
            if (! empty ($pNodes)) {
                $pNode = $pNodes [0];
            } else {
                throw new Exception ("Impossible to insert a right leaf if parent's ref node doesn't exists");
            }
            
            $table = $this->_mapper->getTable ();
            $name = $table->info (Zend_Db_Table::NAME);
            $dba = $table->getAdapter ();
            try {
                $dba->query ("lock tables `$name` write");
                $dba->query ("update `$name` set `$right` = `$right` + 2 where `$right` >= " . $rNode [$right]);
                $dba->query ("update `$name` set `$left` = `$left` + 2 where `$left` >= " . $rNode [$left]);
                $modelClass = $this->_mapper->getModelName ();
                $leaf = new $modelClass ();
                $leaf->setDonnees ($node);
                $leaf->set ($right, $rNode [$right] + 2);
                $leaf->set ($left, $rNode [$right] + 1);
                $leaf->save ();
                $dba->query ("unlock tables");
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        public function createChildLeaf ($parent, $leaf) {
            $pNode = $this->getNode ($parent);
            if ($pNode) {
                list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
                $table = $this->_mapper->getTable ();
                $name = $table->info (Zend_Db_Table::NAME);
                $dba = $table->getAdapter ();
                try {
                    $dba->query ("lock tables `$name` write");
                    $dba->query ("update `$name` set `$left` = `$left` + 2 where `$left` >= " . $pNode->$right);
                    $dba->query ("update `$name` set `$right` = `$right` + 2 where `$right` >= " . $pNode->$right);
                    $modelName = $this->_mapper->getModelName ();
                    $tree = new $modelName ();
                    $tree->setDonnees ($leaf);
                    $tree->setDonnees (array (
                        $this->_fields->left => $pNode->$right,
                        $this->_fields->right => $pNode->$right + 1,
                        $this->_fields->level => $pNode->$level + 1
                    ));
                    $id = $this->_mapper->save ($tree);
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                throw new Exception ("Invalid parent node");
            }
            return $id;
        }
        
        public function remove ($id) {
            $node = $this->getNode ($id);
            if ($node) {
                $left = $this->_fields->left;
                $right = $this->_fields->right;
                $delta = $node->$right - $node->$left + 1;
                $table = $this->_mapper->getTable ();
                $name = $table->info (Zend_Db_Table::NAME);
                $dba = $table->getAdapter ();
                try {
                    $dba->query ("lock tables `$name` write");
                    $dba->query ("delete from `$name` where `$left` >= " . $node->$left . " and `$right` <= " . $node->$right);
                    $dba->query ("update `$name` set `$left` = `$left` - $delta where `$left` > " . $node->$left);
                    $dba->query ("update `$name` set `$right` = `$right` - $delta where `$right` > " . $node->$right);
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                // noeud non existant
            }
            return $this;
        }
                
        /**
         * 
         * @param int $id Id du noeud à déplacer
         * @param int $target Id du noeud cible
         * @param int $where 0=dessous, 1=a droite, 2=a gauche
         */
        public function move ($id, $target, $where = 0) {
            $node = $this->getNode ($id);
            $tNode = $this->getNode ($target);
            if ($node && $tNode) {
                list ($ident, $left, $right, $level) = $this->_getFieldsNames ();
                $delta = $node->$right - $node->$left + 1;
                $table = $this->_mapper->getTable ();
                $name = $table->info (Zend_Db_Table::NAME);
                $dba = $table->getAdapter ();
                try {
                    if (! $where) {
                        $newLeft = $tNode->$left + 1;
                    } else {
                        $children = $this->getChildren ($target);
                        $keys = array_keys ($children);
                        if (! isset ($keys [$where - 2])) {
                            if (isset ($keys [$where - 1])) {
                                $newLeft = $children [$keys [$where - 2]][$right] + 1;
                            } else {
                                throw new Exception ('Impossible !');
                            }
                        }
                        $newLeft = $children [$keys [$where - 2]][$right] + 1;
                    }

                    // On déplace le bloc avec left et right en négatif
                    $dba->query ("lock tables `$name` write");
                    $sql = "update `$name` ";
                    $sql .= "set `$left` = -(`$left` - " . $node->$left . " + 1), ";
                    $sql .= "`$right` = -(`$right` - " . $node->$left . " + 1), ";
                    $sql .= "`$level` = `$level` + (1 * " . ($tNode->$level - $node->$level) . ") + 1 ";
                    $sql .= "where `$left` >= " . $node->$left . " ";
                    $sql .= "and `$right` <= " . $node->$right;
                    $dba->query ($sql);
                    
                    // On bouche le trou
                    $dba->query ("update `$name` set `$left` = `$left` - $delta where `$left` > " . $node->$left);
                    $dba->query ("update `$name` set `$right` = `$right` - $delta where `$right` > " . $node->$left);
                    
                    // On recalcule la nouvelle position au cas où on déplace dans le même niveau
                    // ou que la destination a changé
                    $tNode = $this->getNode ($target);
                    if ($where) {
                        $children = $this->getChildren ($target);
                        $keys = array_keys ($children);
                        if (isset ($keys [$where - 2])) {
                            $newLeft = $children [$keys [$where - 2]][$right] + 1;
                        } elseif (isset ($keys [$where - 1])) {
                            $newLeft = $children [$keys [$where - 1]][$right] + 1;
                        } else {
                            $newLeft = $tNode->$right;
                        }
                    } else {
                        $newLeft = $tNode->$left + 1;
                    }
                    
                    // On écarte les noeuds > à la nouvelle position pour faire la place
                    $dba->query ("update `$name` set `$left` = `$left` + $delta where `$left` >= $newLeft");
                    $dba->query ("update `$name` set `$right` = `$right` + $delta where `$right` >= $newLeft");
                   
                    // On remet les noeud à déplacer à leur place définitive
                    $sql = "update `$name` ";
                    $sql .= "set `$left` = -1 * `$left` + $newLeft - 1, ";
                    $sql .= "`$right` = -1 * `$right` + $newLeft - 1 ";
                    $sql .= "where `$left` < 0";
                    $dba->query ($sql);
                    
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                //throw new Exception ('No way to move from nowhere or to nowhere !');
            }
        }
        
        public function update ($id, $data) {
            try {
                $node = $this->getNode ($id);
                if ($node) {
                    $keys = $this->_getFieldsNames ();
                    foreach ($keys as $k) {
                        if (isset ($data [$k])) unset ($data [$k]);
                    }
                    $node->setDonnees ($data);
                    $this->_mapper->save ($node);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
    }