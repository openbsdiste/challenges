<?php
    class Restmoderateur_SaisieController extends App_Controller_RestAction {
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
            $data = array (
                'statut' => trim ($this->getParam ('statut', '')),
                'bloquant' => ($this->getParam ('bloquant', 'No') == 'No') ? 0 : 1
            );
            $id = $this->getParam ('id', '');
            
            $metierStatuts = new Challenge_Model_Metier_Statuts ();
            $metierStatuts->sauve ($id, $data);
            
            $this->_response->Ok ();

            die (); // On ne doit rien renvoyer du tout...
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }