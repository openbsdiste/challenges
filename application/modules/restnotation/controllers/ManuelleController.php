<?php
    class Restnotation_ManuelleController extends App_Controller_RestAction {
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
                $clubId = $this->getParam ('club', false);
                if ($clubId === false) {
                    $this->_response->notAcceptable ();
                } else {
                    $liste = array ();
                    $metierArbre = new Challenge_Model_Metier_Arbre ($zsn->chalId);
                    $metierQuestions = new Challenge_Model_Metier_Questions ($zsn->chalId);
                    $metierReponses = new Challenge_Model_Metier_Reponses ($zsn->chalId, $clubId);
                    $arbre = $metierArbre->getArbre ();
                    foreach ($arbre as $feuille) {
                        $q = $metierQuestions->getQuestion ($feuille ['noeud']);
                        $r = $metierReponses->getReponse ($feuille ['noeud']);
                        $liste [] = array (
                            'id' => $feuille ['noeud'],
                            'level' => $feuille ['level'],
                            'titre' => $feuille ['title'],
                            'valeur' => $q ['valeur'],
                            'information' => $q ['information'],
                            'note' => ($r ['note'] >= 0) ? $r ['note'] : ''
                        );
                    }
                    $this->_response->Ok ();
                }
                $this->view->data = $liste;
            } else {
                $this->view->data = array ();
                $this->_response->notAcceptable ();
            }
        }
        
        public function putAction() {
            $data = array ();
            $this->_response->notAcceptable ();
            $this->view->data = $data;
        }
    }