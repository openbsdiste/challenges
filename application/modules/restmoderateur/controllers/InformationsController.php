<?php
    class Restmoderateur_InformationsController extends App_Controller_RestAction {
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
                    if (substr ($k, 0, 6) == 'valeur') {
                        $id = substr ($k, 6);
                    }
                }
                $question = $metier->getQuestion ($id);
                if (isset ($_POST ['info' . $id]) && isset ($_POST ['valeur' . $id])) {
                    $question ['information'] = 1;
                    $question ['valeur'] = '';
                } else {
                    $question ['information'] = 0;
                    $question ['valeur'] = $_POST ['valeur' . $id];
                }
                if (isset ($_POST ['auteur' . $id])) {
                    $question ['auteur'] = trim ($_POST ['auteur' . $id]);
                }
                if (isset ($_POST ['statut' . $id])) {
                    $question ['statut'] = $_POST ['statut' . $id];
                }
                if (
                    ! empty ($_FILES) 
                    && isset ($_FILES ['imgsrc' . $id]) 
                    && (substr ($_FILES ['imgsrc' . $id]['type'], 0, 5) == 'image')
                    && ($_FILES ['imgsrc' . $id]['error'] == 0)
                ) {
                    if ($question ['image'] != '') {
                        $metier->unsetFile ($id, $question ['image']);
                    }
                    $content = file_get_contents ($_FILES ['imgsrc' . $id]['tmp_name']);
                    $metier->setFile ($id, $_FILES ['imgsrc' . $id]['name'], $content);
                    $question ['image'] = $_FILES ['imgsrc' . $id]['name'];
                }
                if (isset ($_POST ['suppr' . $id])) {
                    if ($question ['image'] != '') {
                        $metier->unsetFile ($id, $question ['image']);
                        $question ['image'] = '';
                    }
                }
                $metier->setQuestion ($question);
                $mapper = new Challenge_Model_Mapper_Arbre ();
                $modifie = (isset ($_POST ['modifie' . $id])) ? 1 : 0;
                $mapper->updateModifie ($id, $modifie, $chalId);
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