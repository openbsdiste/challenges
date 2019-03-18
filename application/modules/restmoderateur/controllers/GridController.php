<?php
    class Restmoderateur_GridController extends App_Controller_RestAction {
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
            $page = $this->getParam ('page');
            $limit = $this->getParam ('rows');
            $sidx = $this->getParam ('sidx', 1);
            $sord = $this->getParam ('sord');
            $search = ($this->getParam ('_search', false) == 'true') ? true : false;
            $filters = $this->getParam ('filters', array ());
            
            $metierStatuts = new Challenge_Model_Metier_Statuts ();

            $rows = $metierStatuts->getGridRows ($page, $limit, $sidx, $sord, $search, $filters);
            foreach ($rows as $k => $v) {
                $this->view->$k = $v;
            }
            $this->_response->Ok ();
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }