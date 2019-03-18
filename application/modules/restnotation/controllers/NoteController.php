<?php
    class Restnotation_NoteController extends App_Controller_RestAction {
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
            $id = 0;
            foreach ($this->getAllParams () as $k => $v) {
                if (substr ($k, 0, 4) == "note") {
                    $id = substr ($k, 4);
                }
            }
            $club = intval (App_Crypto::festelFloat ($this->getParam ("club$id", 0), $id));
            if ($id > 0) {
                $zsn = new Zend_Session_Namespace ('challenge');
                $metierChallenge = new Model_Metier_Challenges ();
                $challenge = $metierChallenge->getChallenge ($zsn->chalId);
                $metierReponse = new Challenge_Model_Metier_Reponses ($challenge, $club);
                $reponse = $metierReponse->getReponse ($id);
                $ancienneNote = $reponse ['note'];
                $nouvelleNote = $this->getParam ("note$id");
                $reponse ['note'] = $nouvelleNote;
                $reponse ['moderation'] = 1;
                $metierReponse->setReponse ($reponse);
                $metierQuestion = new Challenge_Model_Metier_Questions ($challenge);
                $question = $metierQuestion->getQuestion ($id);
                if (($question ['clubreponse'] == $club) && ! isset ($_POST ["util$id"])) {
                    $question ['clubreponse'] = 0;
                    $metierQuestion->setQuestion ($question);
                } elseif (isset ($_POST ["util$id"])) {
                    $question ['clubreponse'] = $club;
                    $metierQuestion->setQuestion ($question);
                }
                $metierParticipant = new Challenge_Model_Metier_Participants ($challenge);
                $participant = $metierParticipant->getParticipant ($club);
                if ($participant ['calculee'] == -1) {
                    $participant ['calculee'] = 0;
                } elseif ($ancienneNote != -1) {
                    $participant ['calculee'] -= $ancienneNote;
                }
                $participant ['calculee'] += $nouvelleNote;
                $metierParticipant->setParticipant ($participant);
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