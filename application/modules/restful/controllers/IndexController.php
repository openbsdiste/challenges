<?php
    class Restful_IndexController extends App_Controller_RestAction {
        /**
         * @var Restful_Model_Metier_Tree
         */
        protected $_tree;
        
        public function init () {
            parent::init();
            $this->_tree = new Restful_Model_Metier_Tree ();
        }
        
        public function indexAction() {
            $this->_response->notAcceptable ();
        }
        
        public function deleteAction() {
            $this->_response->notAcceptable ();
        }
        
        public function getAction() {
            $id = $this->getParam ('id', 0);
            
            $operation = $this->getParam ('operation', 'inconnue');
            switch ($operation) {
                case 'getChildren' : $data = $this->_tree->getChildren ($id); $this->_response->ok (); break;
                default : $data = array (); $this->_response->notAcceptable (); break;
            }
            
            $this->view->data = $data;
        }

        public function postAction() {
            $id = $this->getParam ($id, 0);
            
            $operation = $this->getParam ('operation', 'inconnue');
            switch ($operation) {
                case 'moveNode' : 
                    $dataSrc = array (
                        'id' => $id,
                        'ref' => $this->getParam ('title', -1),
                        'position' => $this->getParam ('position', -1),
                        'title' => $this->getParam ('title', ''),
                        'copy' => $this->getParam ('copy', -1)
                    );
                    $data = $this->_tree->moveNode ($dataSrc);
                    $this->_response->ok ();
                    break;
                default : $data = array (); $this->_response->notAcceptable (); break;
            }
            
            $this->view->data = $data;
        }
        
        public function putAction() {
            $this->_response->notAcceptable ();
        }
    }