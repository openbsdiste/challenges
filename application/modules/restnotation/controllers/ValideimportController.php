<?php
    class Restnotation_ValideimportController extends App_Controller_RestAction {
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
            $chalid = intval ($this->getParam ('chalid', 0));
            if ($club > 0) {
                $zsn = new Zend_Session_Namespace ('challenge');
                if ($zsn->chalId == $chalid) {
                    $metierChallenge = new Model_Metier_Challenges ();
                    $challenge = $metierChallenge->getChallenge ($zsn->chalId);
                    $metierParticipant = new Challenge_Model_Metier_Participants ($challenge);
                    $metierParticipant->supprimeParticipant ($club);
                    $metierReponse = new Challenge_Model_Metier_Reponses ($challenge, $club);
                    $post = $this->getAllParams ();
                    $liste = array ();
                    foreach ($post as $k => $v) {
                        $d = substr ($k, 0, 2);
                        $r = substr ($d, 0, 1) . 'r';
                        $i = substr ($k, 2);
                        if (($d == 'sn') || ($d == 'rn')) {
                            $liste [$v] = $this->getParam ($r . $i, '');
                        }
                    }
                    
                    foreach ($liste as $noeud => $reponse) {
                        $r = $metierReponse->getReponse ($noeud);
                        $r ['moderation'] = 1;
                        $r ['crypte'] = 0;
                        $r ['texte'] = $reponse;
                        $metierReponse->setReponse ($r);
                    }
                    $participant = $metierParticipant->getParticipant ($club);
                    $participant ['calculee'] = 0;
                    $participant ['club'] = $club;
                    $metierParticipant->setParticipant ($participant);
                    $this->_response->Ok ();
                } else {
                    $this->_response->notAcceptable ();
                }
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