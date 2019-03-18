<?php
    class Restutilisateur_PingController extends App_Controller_RestAction {
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
                $ok = true;
                if (is_string ($challenge ['datelimite']) && ($challenge ['datelimite'] != '')) {
                    $date = implode ('', array_reverse (explode ('/', $challenge ['datelimite'])));
                    $ok = ($date >= date ('Ymd'));
                }
            } else {
                $ok = false;
            }
            $data = array (
            );
            if ($ok) {
                $this->_response->Ok ();
            } else {
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