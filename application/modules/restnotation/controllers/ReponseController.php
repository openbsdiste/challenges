<?php
    class Restnotation_ReponseController extends App_Controller_RestAction {
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
            $id = $this->getParam ('qid', 0);
            $club = intval (App_Crypto::festelFloat ($this->getParam ('club', 0), $id));
            $zsn = new Zend_Session_Namespace ('challenge');
            if (isset ($zsn->chalId)) {
                $chalId = $this->getParam ('chalid', $zsn->chalId);
                $metierChallenge = new Model_Metier_Challenges ();
                $challenge = $metierChallenge->getChallenge ($chalId);
                $metierQuestion = new Challenge_Model_Metier_Questions ($challenge);
                $question = $metierQuestion->getQuestion ($id);
                $metierReponse = new Challenge_Model_Metier_Reponses ($challenge, $club);
                $reponse = $metierReponse->getReponse ($id);
                $documents = $metierReponse->getFichiers ($id);
                if ($reponse ['note'] < 0) {
                    $reponse ['note'] = '';
                }
                $data = array (
                    'documents' => $documents,
                    'reponse' => $reponse ['texte'],
                    'note' => $reponse ['note'],
                    'estReponse' => ($question ['clubreponse'] == $club) ? "1" : "0"
                );

                $this->_response->Ok ();
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
