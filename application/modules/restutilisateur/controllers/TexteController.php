<?php
    class Restutilisateur_TexteController extends App_Controller_RestAction {
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
                $identite = Zend_Auth::getInstance ()->getIdentity ();
                $metier = new Challenge_Model_Metier_Reponses ($challenge, $identite->id);
                $id = 0;
                foreach ($_POST as $k => $v) {
                    if (substr ($k, 0, 5) == 'texte') {
                        $id = substr ($k, 5);
                    } elseif (substr ($k, 0, 11) == 'noteinterne') {
                        $id = substr ($k, 11);
                    } elseif (substr ($k, 0, 7) == 'reponse') {
                        $id = substr ($k, 7);
                    }
                }
                $reponse = $metier->getReponse ($id);
                if (isset ($_POST ['texte' . $id])) {
                    $reponse ['texte'] = $_POST ['texte' . $id];
                } elseif (isset ($_POST ['noteinterne' . $id])) {
                    $reponse ['notesinternes'] = $_POST ['noteinterne' . $id];
                } elseif (isset ($_POST ['reponse' . $id])) {
                    $reponse ['texte'] = $_POST ['reponse' . $id];
                }
                $metier->setReponse ($reponse);
                $data = array (
                    'id' => $id
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