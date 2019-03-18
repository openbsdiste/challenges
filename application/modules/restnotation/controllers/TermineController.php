<?php
    class Restnotation_TermineController extends App_Controller_RestAction {
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
            if (isset ($zsn->chalId)) {
                $metierChallenge = new Model_Metier_Challenges ();
                
                $valCompte = $metierChallenge->validationCompte ($zsn->chalId);
                $questions = $valCompte ['questions'];
                $documents = $valCompte ['documents'];

                foreach ($questions as $q) {
                    $metierChallenge->validationQuestion ($zsn->chalId, $q);
                }

                foreach ($documents as $d) {
                    $metierChallenge->validationDocument ($zsn->chalId, $d ['id'], $d ['nom']);
                }
                $metierChallenge->validationOuvre ($zsn->chalId);

                $metierChallenge->termineChallenge ($zsn->chalId);
                $this->_response->Ok ();
            } else {
                $this->_response->notAcceptable ();
            }
            $this->view->data = array ();
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }