<?php
    class Authentification_Model_Metier_Utilisateurs extends App_Model_Metier {
        protected $_mapperUtilisateurs;

        protected function _getFilteredSelect ($search, $filters) {
            if ($search && ! empty ($filters)) {
                $select = $this->_mapperUtilisateurs->select ();
                $filters = json_decode ($filters);
                foreach ($filters->rules as $rule) {
                    $field = $rule->field;
                    $data = trim ($rule->data);
                    switch ($field) {
                        case "login":
                        case "email":
                        case "club":
                            if ($data != '') {
                                $select->where ("$field like '%$data%'");
                            }
                            break;
                        case "role":
                            if ($data != 'tous') {
                                $select->where ("role = '$data'");
                            }
                            break;
                        case "actif":
                            if ($data != 'tous') {
                                $valeur = ($data == 'on') ? 1 : 0;
                                $select->where ("actif=$valeur");
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
            $this->_mapperUtilisateurs = new Authentification_Model_Mapper_Utilisateurs ();
        }

        public function getListeClubs () {
            // La liste des clubs doit inclure les organisateurs s'il y a plusieurs challenges en même temps...
            //$liste = $this->_mapperUtilisateurs->fetchAll ("actif='1' and role='utilisateur'", 'club ASC');
            $liste = $this->_mapperUtilisateurs->fetchAll ("actif='1' and (role='utilisateur' or role='moderateur')", 'club ASC');
            $clubs = array ();
            foreach ($liste as $club) {
                $clubs [$club->id] = $club->club;
            }
            return $clubs;
        }

        public function trouve ($id) {
            return $this->_mapperUtilisateurs->find ($id);
        }

        public function setUtilisateur (Authentification_Model_Utilisateurs $utilisateur) {
            $this->_mapperUtilisateurs->save ($utilisateur);
        }

        public function changePassword ($id, $password, $memoire = '') {
            $utilisateur = $this->trouve ($id);
            $utilisateur->password = md5 ($password);
            $utilisateur->memoire = $memoire;
            $this->setUtilisateur ($utilisateur);
        }

        public function getGridRows ($page, $limit, $sidx, $sord = 'ASC', $search = false, $filters = array ()) {
            $selectCount = $this->_getFilteredSelect ($search, $filters);
            $count = $this->_mapperUtilisateurs->getCount ($selectCount);

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
            $res = $this->_mapperUtilisateurs->fetchAll ($selectAll, "$sidx $sord", $limit, $start);

            foreach ($res as $r) {
                $rows ['rows'][] = array (
                    'id' => $r->id,
                    'cell' => array_values ($r->getDonnees (true))
                );
            }

            return $rows;
        }
    }
