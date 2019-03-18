<?php
    class Restadministrateur_SaisieController extends App_Controller_RestAction {
        public function init () {
            parent::init();
        }
        
        public function indexAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
        
        public function deleteAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
        
        public function getAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }

        public function postAction() {
            $user = array (
                'actif' => ($this->getParam ('actif', 'No') == 'No') ? 0 : 1,
                'club' => trim ($this->getParam ('club', '')),
                'email' => trim ($this->getParam ('email', '')),
                'id' => $this->getParam ('id', null),
                'login' => trim ($this->getParam ('login', '')),
                'password' => trim ($this->getParam ('password', '')),
                'role' => trim ($this->getParam ('role', 'utilisateur')),
                'memoire' => trim ($this->getParam ('memoire', ''))
            );
            
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $utilisateur = $metierUtilisateurs->trouve ($user ['id']);
            if ($utilisateur !== false) {
                $login = strtoupper ($login);
                if ($user ['password'] != $utilisateur->password) {
                    $user ['password'] = md5 ($user ['password']);
                }
            } else {
                $utilisateur = new Authentification_Model_Utilisateurs ();
                $user ['id'] = null;
                $user ['password'] = md5 ($user ['password']);
            }
            $utilisateur->setDonnees ($user);
            
            $metierUtilisateurs->setUtilisateur ($utilisateur);
            $this->_response->Ok ();

            die (); // On ne doit surtout rien renvoyer du tout...
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }