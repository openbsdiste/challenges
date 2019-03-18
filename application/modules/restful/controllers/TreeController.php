<?php
    class Restful_TreeController extends App_Controller_RestAction {
        /**
         * @var Restful_Model_Metier_Mytree
         */
        protected $_tree;
        protected $_chalId;
        
        public function init () {
            parent::init();

            $zsn = new Zend_Session_Namespace ('challenge');
            $this->_tree = new Challenge_Model_Metier_Arbre ($zsn->chalId);
            $this->_chalId = $zsn->chalId;
        }
        
        public function indexAction() {
            $this->_response->notAcceptable ();
        }
        
        public function deleteAction() {
            $id = $this->getParam('id', 0);
            
            if ($id > 0) {
                $liste = $this->_tree->deleteNode ($id);
                $metierChal = new Model_Metier_Challenges ();
                $metier = new Challenge_Model_Metier_Questions ($metierChal->getChallenge ($this->_chalId));
                $metier->unsetQuestions ($liste);
                $this->view->data = array ('id' => $liste);
                $this->_response->ok ();
            } else {
                $this->_response->notAcceptable ();
                $this->view->data = array ();
            }
        }
        
        public function getAction() {
            $id = $this->getParam ('id', 0);
            
            $operation = $this->getParam ('operation', 'inconnue');

            switch ($operation) {
                case 'getChildren' : $data = $this->_tree->getChildren ($id); $this->_response->ok (); break;
                default : $data = array (); $this->_response->notAcceptable (); break;
            }
            
            $this->view->data = $data;
        }

        public function postAction() {
            $id = $this->getParam ('id', -1);
            $copy = $this->getParam ('copy', 0);
            $operation = $this->getParam ('operation', 'inconnue');
            $position = $this->getParam ('position', 0);
            $ref = $this->getParam ('ref', -1);

            $metierChallenge = new Model_Metier_Challenges ();
            $metierQuestions = new Challenge_Model_Metier_Questions ($metierChallenge->getChallenge ($this->_chalId));
            $metierStatuts = new Challenge_Model_Metier_Statuts ();
            
            switch ($operation) {
                case 'moveNode' :
                    if ($copy) {
                        $this->_response->notAcceptable ();
                    } else {
                        $this->view->data = $this->_tree->moveNode ($id, $ref, $position);
                        $this->_response->ok ();
                    }
                    break;
                case 'getAuthors' : 
                    $this->view->data = $metierQuestions->getListeAuteurs ();
                    $this->_response->ok ();
                    break;
                case 'getQuestionsByAuthor':
                    $this->view->data = $metierQuestions->getListeQuestionsAuteur ($this->getParam ('auteur', ''));
                    $this->_response->ok ();
                    break;
                case 'getStatuts':
                    $this->view->data = $metierStatuts->getAllStatuts ();
                    $this->_response->ok ();
                    break;
                case 'getQuestionsByStatut':
                    $this->view->data = $metierQuestions->getListeQuestionsStatut ($this->getParam ('statut', ''));
                    $this->_response->ok ();
                    break;
                default :
                    $this->view->data = array ();
                    $this->_response->notAcceptable ();
                    break;
            }
        }
        
        public function putAction() {
            $operation = $this->getParam ('operation', '');
            if ($operation == "addNode") {
                $id = $this->_tree->addLeaf ($this->getParam ("parent", 1), array ('title' => $this->getParam ('title')));
                $this->view->data = array ('id' => $id);
                $this->_response->ok ();
            } elseif ($operation == "renameNode") {
                $data = $this->_tree->renameNode ($this->getParam ("id", -1), array ('title' => $this->getParam ('title')));
                $this->view->data = $data;
                $this->_response->ok ();
            } elseif ($operation == "dragQuestion") {
                $id = $this->getParam ('id', '');
                $tid = $this->getParam ('tid', '');
                $qid = $this->getParam ('qid', '');
                $this->view->id = $id;
                $this->view->tid = $tid;
                $this->view->qid = $qid;
                $this->_response->ok ();
            } else {
                $this->_response->notAcceptable ();
            }
        }
    }