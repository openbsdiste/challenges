<?php
    class Restnotation_ValidenotesController extends App_Controller_RestAction {
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
            $notes = $this->getParam ('notes');
            $club = intval ($this->getParam ('club', 0));
            if ($club > 0) {
                $zsn = new Zend_Session_Namespace ('challenge');
                $metierChallenge = new Model_Metier_Challenges ();
                $challenge = $metierChallenge->getChallenge ($zsn->chalId);
                $metierReponse = new Challenge_Model_Metier_Reponses ($challenge, $club);
                $total = 0;
                foreach ($notes as $note) {
                    list ($question, $valeur) = explode ('_', $note);
                    $vorigine = trim ($valeur);
                    $question = intval (substr ($question, 1));
                    $valeur = floatval ($valeur);
                    $reponse = $metierReponse->getReponse ($question);
                    $reponse ['note'] = $valeur;
                    $reponse ['moderation'] = 1;
                    if ($reponse ['texte'] == '') {
                        $reponse ['texte'] = 'Réponse non disponible.';
                    }
//                    if ($vorigine == '') {
//                        $metierReponse->unsetReponse ($question);
//                    } else {
//echo "<pre>"; print_r ($reponse);
                        $metierReponse->setReponse ($reponse);
//die ();
//                    }
                    $total += $valeur;
                }
                $metierParticipant = new Challenge_Model_Metier_Participants ($challenge);
                $participant = $metierParticipant->getParticipant ($club);
                $participant ['calculee'] = $total;
                $participant ['club'] = $club;
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
