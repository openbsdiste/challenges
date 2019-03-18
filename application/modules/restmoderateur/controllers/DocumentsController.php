<?php
    class Restmoderateur_DocumentsController extends App_Controller_RestAction {
        public function init () {
            parent::init();
        }
        
        public function indexAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
        
        public function deleteAction() {
            $zsn = new Zend_Session_Namespace ('challenge');
            if (isset ($zsn->chalId)) {
                $chalId = $this->getParam ('chalid', $zsn->chalId);
                $metierChallenge = new Model_Metier_Challenges ();
                $challenge = $metierChallenge->getChallenge ($chalId);
                $metier = new Challenge_Model_Metier_Questions ($challenge);
                $id = $_POST ['qid'];
                $fichier = urldecode ($_POST ['fic']);
                $metier->unsetFile ($id, $fichier);

                $data = array (
                    'id' => $id,
                    'question' => $metier->getQuestion ($id),
                    'documents' => $metier->getFichiers ($id),
                    'statut' => $challenge ['statut']
                );
                $this->_response->Ok ();
            } else {
                $data = array ();
                $this->_response->notAcceptable ();
            }
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
                foreach ($_FILES as $k => $v) {
                    if (substr ($k, 0, 8) == 'document') {
                        $id = substr ($k, 8);
                    }
                }
                if ($id) {
                    $question = $metier->getQuestion ($id);
                    $metier->setFile ($id, $_FILES ['document' . $id]['name'], file_get_contents ($_FILES ['document' . $id]['tmp_name']));
                }
                $data = array (
                    'id' => $id,
                    'question' => $question,
                    'documents' => $metier->getFichiers ($id),
                    'statut' => $challenge ['statut']
                );
                $this->_response->Ok ();
            } else {
                $data = array ();
                $this->_response->notAcceptable ();
            }
            $this->view->data = $data;
            $this->ieHack ();
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }