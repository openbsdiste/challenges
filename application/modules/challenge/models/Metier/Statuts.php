<?php
    class Challenge_Model_Metier_Statuts extends App_Model_Metier {
        protected $_mapperStatuts;
        
        protected function _getFilteredSelect ($search, $filters) {
            if ($search && ! empty ($filters)) {
                $select = $this->_mapperStatuts->select ();
                $filters = json_decode ($filters);
                foreach ($filters->rules as $rule) {
                    $field = $rule->field;
                    $data = trim ($rule->data);
                    switch ($field) {
                        case "statut":
                            if ($data != '') {
                                $select->where ("$field like '%$data%'");
                            }
                            break;
                        case "bloquant":
                            if ($data != 'tous') {
                                $valeur = ($data == 'on') ? 1 : 0;
                                $select->where ("bloquant=$valeur");
                            }
                            break;
                    }
                }
            } else {
                $select = null;
            }
            return $select;
        }
        
        public function __construct () {
            $this->_mapperStatuts = new Challenge_Model_Mapper_Statuts ();
        }
        
        public function getGridRows ($page, $limit, $sidx, $sord = 'ASC', $search = false, $filters = array ()) {
            $selectCount = $this->_getFilteredSelect ($search, $filters);
            $count = $this->_mapperStatuts->getCount ($selectCount);
            
            if (($count > 0) && ($limit > 0)) {
                $totalPages = ceil ($count / $limit);
            } else {
                $totalPages = 0;
            }
            
            $start = $limit * $page - $limit;
            if ($start < 0) {
                $start = 0;
            }
            
            $rows = array (
                'page' => $page,
                'total' => $totalPages,
                'records' => $count,
                'rows' => array ()
            );
            
            $selectAll = $this->_getFilteredSelect ($search, $filters);
            $res = $this->_mapperStatuts->fetchAll ($selectAll, "$sidx $sord", $limit, $start);
            
            foreach ($res as $r) {
                $rows ['rows'][] = array (
                    'id' => $r->statut,
                    'cell' => array ($r->statut, $r->statut, $r->bloquant)
                );
            }
            
            return $rows;
        }

        public function sauve ($id, $data) {
            if ($this->_mapperStatuts->find ($id) !== false) {
                $this->_mapperStatuts->delete (array ('statut' => $id));
            }
            if ($data ['statut'] != '') {
                $statut = new Challenge_Model_Statuts ();
                $statut->setDonnees ($data);

                $this->_mapperStatuts->save ($statut);
            }
        }
        
        public function getAllStatuts () {
            $list = $this->_mapperStatuts->fetchAll ();
            $all = array ();
            foreach ($list as $v) {
                $all [$v->statut] = $v->statut;
            }
            return $all;
        }
        
        public function getStatutsBloquants () {
            $list = $this->_mapperStatuts->fetchAll ();
            $bloquants = array ();
            foreach ($list as $v) {
                if ($v->bloquant) {
                    $bloquants [$v->statut] = $v->statut;
                }
            }
            return $bloquants;
        }

    }