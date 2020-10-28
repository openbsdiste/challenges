<?php
    class Restmoderateur_TexteController extends App_Controller_RestAction {
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
                $metier = new Challenge_Model_Metier_Questions ($challenge);
                $id = 0;
                foreach ($_POST as $k => $v) {
                    if (substr ($k, 0, 5) == 'texte') {
                        $id = substr ($k, 5);
                    }
                }
                $question = $metier->getQuestion ($id);
                if (isset ($_POST ['texte' . $id])) {
                    $question ['texte'] = $_POST ['texte' . $id];
                }
                $vhq = new App_View_Helper_Question ();
                $preview = $vhq->question ($question, $metier->getFichiers ($id), $challenge ['statut']);
                $metier->setQuestion ($question);
                $data = array (
                    'id' => $id,
                    'preview' => $preview
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
