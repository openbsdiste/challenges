<?php
    class Restutilisateur_VerouillageController extends App_Controller_RestAction {
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
                $chalId = $this->getParam ('chalid', $zsn->chalId);
                $metierChallenge = new Model_Metier_Challenges ();
                $challenge = $metierChallenge->getChallenge ($chalId);
                $metierParticipants = new Challenge_Model_Metier_Participants ($challenge);
                $identite = Zend_Auth::getInstance ()->getIdentity ();
                $participant = $metierParticipants->getParticipant ($identite->id);
                if (isset ($_POST ['t'])) {
                    if ($_POST ['t'] == "definir") {
                        $participant ['password'] = md5 ($_POST ['p']);
                        $metierParticipants->setParticipant ($participant);
                        $zsn->canValidate = false;
                    } elseif (
                        ($_POST ['t'] == "verifier") 
                        && (md5 ($_POST ['p']) == $participant ['password'])
                    ) {
                        $zsn->canValidate = true;
                    }
                    $data = array ();
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