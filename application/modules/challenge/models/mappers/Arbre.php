<?php
    class Challenge_Model_Mapper_Arbre extends App_Model_Mapper_DbTable {
        protected $_tableName = 'Challenge_Model_DbTable_Arbre';

        public function getNode ($noeud, $challenge) {
            try {
                $node = $this->findArray (array ('noeud' => $noeud, 'challenge' => $challenge));
                if (is_array ($node) && ! empty ($node)) {
                    $node = $node [0];
                }
            } catch (Exception $e) {
                throw $e;
            }
            return $node;
        }
        
        public function getNodeCtr ($noeud, $challenge) {
            $node = $this->getNode ($noeud, $challenge);
            $num = '';
            if ($node) {
                $select = $this->select ()
                    ->where ($this->quoteInto ("`left` <= ?", $node->left))
                    ->where ($this->quoteInto ("`right` >= ?", $node->right))
                    ->where ($this->quoteInto ("`challenge` = ?", $challenge))
                    ->order (array ("level", "left"));
                try {
                    $res = $this->fetchAll ($select);
                } catch (Exception $e) {
                    throw $e;
                }
                $list = array ();
                foreach ($res as $element) {
                    $list [] = $element->getDonnees (true);
                }
                for ($i = 0; $i < count ($list) - 2; $i++) {
                    $select = $this->select ()
                        ->where ($this->quoteInto ("`left` >= ?", $list [$i]['left']))
                        ->where ($this->quoteInto ("`right` <= ?", $list [$i + 1]['right']))
                        ->where ($this->quoteInto ("`level` = ?", $list [$i + 1]['level']))
                        ->where ($this->quoteInto ("`challenge` = ?", $challenge))
                        ->order ("left");
                    try {
                        $res = $this->fetchAll ($select);
                        $num .= (count ($res)) . ".";
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
            return $num;
        }

        public function getChildren ($noeud, $challenge, $recursive = false) {
            $children = array ();
            $node = $this->getNode ($noeud, $challenge);
            if ($node) {
                $select = $this->select ()
                    ->where ($this->quoteInto ("`left` > ?", $node->left))
                    ->where ($this->quoteInto ("`right` < ?", $node->right))
                    ->where ($this->quoteInto ("`challenge` = ?", $challenge));
                if (! $recursive) {
                    $select->where ($this->quoteInto ("`level` = ?", ($node->level + 1)));
                }
//                $select->order (array ('level', 'left'));
                $select->order ('left');
                try {
                    $res = $this->fetchAll ($select);
                } catch (Exception $e) {
                    throw $e;
                }
                foreach ($res as $element) {
                    $children [$element->noeud] = $element->getDonnees (true);
                }
            }
            return $children;
        }
        
        public function dropTree ($challenge) {
            try {
                $this->delete (array ('challenge' => $challenge));
                $tree = new Challenge_Model_Arbre ();
                $tree->setDonnees (array (
                    'noeud' => 1,
                    'challenge' => $challenge,
                    'left' => 1,
                    'right' => 4,
                    'level' => 0,
                    'title' => 'ROOT'
                ));
                $this->save ($tree);
                $tree->setDonnees (array (
                    'noeud' => 2,
                    'left' => 2,
                    'right' => 3,
                    'level' => 1,
                    'title' => 'Mot du président'
                ));
                $this->save ($tree);
            } catch (Exception $e) {
                throw $e;
            }
            return $this;
        }
        
        public function remove ($noeud, $challenge) {
            $node = $this->getNode ($noeud, $challenge);
            $enfants = $this->getChildren ($noeud, $challenge, true);
            $liste = array ($noeud);
            foreach ($enfants as $enfant) {
                $liste [] = $enfant ['noeud'];
            }
            if ($node) {
                $delta = $node->right - $node->left + 1;
                $table = $this->getTable ();
                $dba = $table->getAdapter ();
                try {
                    $dba->query ("lock tables `arbre` write");
                    $dba->query ("delete from `arbre` where `left` >= " . $node->left . " and `right` <= " . $node->right . " and `challenge` = $challenge");
                    $dba->query ("update `arbre` set `left` = `left` - $delta where `left` > " . $node->left . " and `challenge` = $challenge");
                    $dba->query ("update `arbre` set `right` = `right` - $delta where `right` > " . $node->right . " and `challenge` = $challenge");
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                // noeud non existant
            }
            return $liste;
        }
        
        public function createChildLeaf ($parent, $leaf, $challenge) {
            $pNode = $this->getNode ($parent, $challenge);
            if ($pNode) {
                $table = $this->getTable ();
                $dba = $table->getAdapter ();
                try {
                    $dba->query ("lock tables `arbre` write");
                    $dba->query ("update `arbre` set `left` = `left` + 2 where `left` >= " . $pNode->right . " and `challenge` = $challenge");
                    $dba->query ("update `arbre` set `right` = `right` + 2 where `right` >= " . $pNode->right . " and `challenge` = $challenge");
                    $max = $table->fetchAll (
                        $table->select ()
                            ->from ($table, array (new Zend_Db_Expr ('max(noeud) as max')))
                            ->where ("challenge = $challenge")
                    )->toArray ();
                    $tree = new Challenge_Model_Arbre ();
                    $tree->setDonnees ($leaf);
                    $tree->setDonnees (array (
                        'noeud' => $max [0]['max'] + 1,
                        'left' => $pNode->right,
                        'right' => $pNode->right + 1,
                        'level' => $pNode->level + 1,
                        'challenge' => $challenge
                    ));
                    $this->save ($tree);
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                throw new Exception ("Invalid parent node");
            }
            return $max [0]['max'] + 1;
        }
        
        public function move ($noeud, $target, $where, $challenge) {
            $node = $this->getNode ($noeud, $challenge);
            $tNode = $this->getNode ($target, $challenge);
            if ($node && $tNode) {
                $delta = $node->right - $node->left + 1;
                $table = $this->getTable ();
                $dba = $table->getAdapter ();
                try {
                    if (! $where) {
                        $newLeft = $tNode->left + 1;
                    } else {
                        $children = $this->getChildren ($target, $challenge);
                        $keys = array_keys ($children);
                        if (! isset ($keys [$where - 2])) {
                            if (isset ($keys [$where - 1])) {
                                $newLeft = $children [$keys [$where - 2]]['right'] + 1;
                            } else {
                                throw new Exception ('Impossible !');
                            }
                        }
                        $newLeft = $children [$keys [$where - 2]]['right'] + 1;
                    }

                    // On déplace le bloc avec left et right en négatif
                    $dba->query ("lock tables `arbre` write");
                    $sql = "update arbre ";
                    $sql .= "set `left` = -(`left` - " . $node->left . " + 1), ";
                    $sql .= "`right` = -(`right` - " . $node->left . " + 1), ";
                    $sql .= "`level` = `level` + (1 * " . ($tNode->level - $node->level) . ") + 1 ";
                    $sql .= "where `left` >= " . $node->left . " ";
                    $sql .= "and `right` <= " . $node->right . " ";
                    $sql .= "and `challenge` = " . $challenge;
                    $dba->query ($sql);
                    
                    // On bouche le trou
                    $dba->query ("update `arbre` set `left` = `left` - $delta where `challenge` = $challenge and `left` > " . $node->left);
                    $dba->query ("update `arbre` set `right` = `right` - $delta where `challenge` = $challenge and `right` > " . $node->left);
                    
                    // On recalcule la nouvelle position au cas où on déplace dans le même niveau
                    // ou que la destination a changé
                    $tNode = $this->getNode ($target, $challenge);
                    if ($where) {
                        $children = $this->getChildren ($target, $challenge);
                        $keys = array_keys ($children);
                        if (isset ($keys [$where - 2])) {
                            $newLeft = $children [$keys [$where - 2]]['right'] + 1;
                        } elseif (isset ($keys [$where - 1])) {
                            $newLeft = $children [$keys [$where - 1]]['right'] + 1;
                        } else {
                            $newLeft = $tNode->right;
                        }
                    } else {
                        $newLeft = $tNode->left + 1;
                    }
                    
                    // On écarte les noeuds > à la nouvelle position pour faire la place
                    $dba->query ("update `arbre` set `left` = `left` + $delta where `challenge` = $challenge and `left` >= $newLeft");
                    $dba->query ("update `arbre` set `right` = `right` + $delta where `challenge` = $challenge and `right` >= $newLeft");
                   
                    // On remet les noeud à déplacer à leur place définitive
                    $sql = "update `arbre` ";
                    $sql .= "set `left` = -1 * `left` + $newLeft - 1, ";
                    $sql .= "`right` = -1 * `right` + $newLeft - 1 ";
                    $sql .= "where `challenge` = $challenge and `left` < 0";
                    $dba->query ($sql);
                    
                    $dba->query ("unlock tables");
                } catch (Exception $e) {
                    throw $e;
                }
            } else {
                //throw new Exception ('No way to move from nowhere or to nowhere !');
            }
        }
        
        public function update ($noeud, $data, $challenge) {
            try {
                $node = $this->getNode ($noeud, $challenge);
                if ($node) {
                    $node->setDonnees (array ('title' => $data ['title']));
                    $this->save ($node);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function updateModifie ($noeud, $modifie, $challenge) {
            try {
                $node = $this->getNode ($noeud, $challenge);
                if ($node) {
                    $node->setDonnees (array ('modifie' => $modifie));
                    $this->save ($node);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }
    }