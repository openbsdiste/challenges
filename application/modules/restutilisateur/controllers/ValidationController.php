<?php
    class Restutilisateur_ValidationController extends App_Controller_RestAction {
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
            $zsn = new Zend_Session_Namespace ('challenge');
            $action = $this->getParam ('action', '');
            if (isset ($zsn->chalId)) {
                $chalId = $this->getParam ('chalid', $zsn->chalId);
                $metier = new Model_Metier_Challenges ();
                switch ($action) {
                    case "compte":
                        $data = $metier->validationCompte ($chalId);
                        break;
                    case "question":
                        $data = $metier->validationQuestion ($chalId, $this->getParam ('id', 0));
                        break;
                    case "document":
                        $data = $metier->validationDocument ($chalId, $this->getParam ('id', 0), $this->getParam ('nom', ''));
                        break;
                    case "ouvre":
                        $data = $metier->validationOuvre ($chalId);
                        break;
                    default:
                        $data = false;
                }
                if (is_array ($data)) {
                    $this->_response->Ok ();
                } else {
                    $data = array ();
                    $this->_response->notAcceptable ();
                }
            } else {
                $data = array ();
                $this->_response->notAcceptable ();
            }
            $this->view->data = $data;
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }