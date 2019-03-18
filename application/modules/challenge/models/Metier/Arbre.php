<?php
    class Challenge_Model_Metier_Arbre extends App_Model_Metier {
        protected $_mapperArbre;
        protected $_chalId;
        
        public function __construct ($chalId) {
            $this->_mapperArbre = new Challenge_Model_Mapper_Arbre ();
            $this->_chalId = $chalId;
        }

        public function getChildren ($noeud) {
            $children = $this->_mapperArbre->getChildren ($noeud, $this->_chalId);
            if (($noeud == 1) && empty ($children)) {
                $this->_mapperArbre->dropTree ($this->_chalId);
                $children = $this->_mapperArbre->getChildren ($noeud, $this->_chalId);
            }
            
            $data = array ();
            if ($noeud > 0) {
                $racine = '';
                $num = 1;
                foreach ($children as $k => $v) {
                    if ($racine == '') {
                        $racine = $this->_mapperArbre->getNodeCtr ($k, $this->_chalId);
                    }
                    $data [] = array (
                        'attr' => array ('id' => 'node_' . $k, 'rel' => 'folder_mod', 'num' => $racine . $num++ . '.', 'type' => ($v ['modifie']) ? "modified" : "unmodified"),
                        'data' => $v ['title'],
                        'state' => ($v ['right'] - $v ['left'] > 1) ? 'closed' : '' 
                    );
                }
            }
            return $data;
        }
        
        public function deleteNode ($noeud) {
            if ($noeud > 0) {
                return $this->_mapperArbre->remove ($noeud, $this->_chalId);
            }
            return array ();
        }
        
        public function addLeaf ($parent, $leaf) {
            return $this->_mapperArbre->createChildLeaf ($parent, $leaf, $this->_chalId);
        }
        
        public function moveNode ($noeud, $target, $where) {
            $this->_mapperArbre->move ($noeud, $target, $where, $this->_chalId);
            return array ('status' => 1, 'id' => $noeud);
        }
        
        public function renameNode ($noeud, $data) {
            if ($data ['title']) $data ['title'] = trim ($data ['title']);
            if (! $data ['title'] || ($data ['title'] == '')) {
                $status = 0;
                $message = 'Impossible de modifier, le nom est vide';
            } else {
                $this->_mapperArbre->update ($noeud, $data, $this->_chalId);
                $status = 1;
                $message = '';
            }
            return array ('status' => $status, 'message' => $message, 'id' => $noeud, 'title' => $data ['title']);
        }
        
        public function getArbre () {
            return $this->_mapperArbre->getChildren (1, $this->_chalId, true);
        }
    }