<?php
    class Challenge_IndexController extends App_Controller_Challenge {        
        public function init () {
            $chalId = $this->getParam ('chalid', null);
            if ($chalId !== null) {
                $zsn = new Zend_Session_Namespace ('challenge');
                $zsn->chalId = $chalId;
            }
            parent::init ();
        }
        
        public function indexAction () {
            $ok = true;
            $date = date ('Ymd');
            if (is_string ($this->_chalInfos ['datelimite']) && ($this->_chalInfos ['datelimite'] != '')) {
                $date = implode ('', array_reverse (explode ('/', $this->_chalInfos ['datelimite'])));
                $ok = ($date >= date ('Ymd'));
            }
            if ($this->_chalInfos ['statut'] == Model_Enum_StatutChallenge::ELABORATION) {
                if (($this->_role != Model_Enum_Roles::MODERATEUR) || ($this->_chalInfos ['organisateur'] != $this->_identity->id)) {
                    $this->redirect ('challenge/index/interdit');
                } else {
                    $this->redirect ('challenge/elaboration/index');
                }
            } elseif ($this->_chalInfos ['statut'] == Model_Enum_StatutChallenge::ENCOURS) {
                if ($this->_role == Model_Enum_Roles::UTILISATEUR) {
                    $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);
                    $participant = $metierParticipants->getParticipant ($this->_identity->id);
                    if (! $participant ['valide']) {
                        if ($ok) {
                            $this->redirect ('challenge/reponse/index');
                        }
                    }
                } elseif ($this->_role == Model_Enum_Roles::MODERATEUR) {
                    if ($this->_chalInfos ['organisateur'] == $this->_identity->id) {
                        $this->redirect ('challenge/elaboration/index');
                    } else {
                        if ($ok) {
                            $this->redirect ('challenge/reponse/index');
                        }
                    }
                }
            } elseif ($this->_chalInfos ['statut'] == Model_Enum_StatutChallenge::TERMINE) {
                if (($this->_role == Model_Enum_Roles::MODERATEUR) && ($this->_chalInfos ['organisateur'] == $this->_identity->id)) {
                    $this->redirect ('challenge/notation/index');
                }
            }
            
            $this->view->challenge = $this->_chalInfos;
        }
        
        public function interditAction () {
            $this->view->challenge = $this->_chalInfos;
        }
        
        public function imageAction () {
            $id = $this->getParam ('id', "0");
            
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->image = $metierQuestions->getImage ($id);
        }
        
        public function fichierAction () {
            $id = $this->getParam ('id', "0");
            $nom = urldecode ($this->getParam ('nom', ""));
            
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->fichier = $metierQuestions->getFile ($id, $nom);
            $this->view->nom = $nom;
        }
        
        public function fichierreponseAction () {
            $cid = $this->getParam ('cid', 0);
            $id = $this->getParam ('id', "0");
            $nom = urldecode ($this->getParam ('nom', ""));
            
            $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $cid);

            $this->disableLayout ();
            $this->view->fichier = $metierReponses->getFile ($id, $nom);
            $this->view->nom = $nom;
        }
        
        public function accueilAction () {
            $this->disableLayout ();
            $this->view->challenge = $this->_chalInfos;
        }
        
        public function questionAction () {
            $id = substr ($this->getParam ('id', "node_0"), 5);
            
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $question = $metierQuestions->getQuestion ($id);
            if (
                ($this->_chalInfos ['statut'] == Model_Enum_StatutChallenge::VALIDE) 
                && $question ['clubreponse']
                && ! $question ['information']
            ) {
                $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $question ['clubreponse']);
                $reponse = $metierReponses->getReponse ($id);
                $fichiersReponse = $metierReponses->getFichiers ($id);
           } else {
                $reponse = false;
                $fichiersReponse = array ();
            }

            $this->disableLayout ();
            $this->view->id = $id;
            $this->view->challenge = $this->_chalInfos;
            $this->view->questionData = $question;
            $this->view->questionFiles = $metierQuestions->getFichiers ($id);
            $this->view->reponseData = $reponse;
            $this->view->reponseFiles = $fichiersReponse;
            $this->view->titre = ($question ['information']) ? 'Information' : 'Question';
        }
        
        public function completAction () {
            $fullpage = $this->getParam ('fullpage', false);
            
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            
            if ($fullpage) {
                $this->_helper->layout->setLayout ("preview");
                $this->view->fullpage = true;
            } else {
                $this->disableLayout ();
                $this->view->fullpage = false;
            }
            $this->view->arbre = $metierArbre->getArbre ();
            $this->view->chalInfos = $this->_chalInfos;
            $this->view->mquest = $metierQuestions;
        }
        
        public function resultatsAction () {
            $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->liste = $metierParticipants->getResultats ();
        }
        
        public function reponseAction () {
            $fullpage = $this->getParam ('fullpage', false);
            $id = $this->getParam ('id', 0);
            
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $utilisateur = $metierUtilisateurs->trouve ($id);

            if ($fullpage) {
                $this->_helper->layout->setLayout ("preview");
                $this->view->fullpage = true;
            } else {
                $this->disableLayout ();
                $this->view->fullpage = false;
            }

            $this->view->arbre = $metierArbre->getArbre ();
            $this->view->chalInfos = $this->_chalInfos;
            $this->view->nomParticipant = $utilisateur->club;
            $this->view->mrep = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $id);
            $this->view->id = $id;
        }
        
        public function reponsecorrectionAction () {
            $fullpage = $this->getParam ('fullpage', false);
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestion = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            if ($fullpage) {
                $this->_helper->layout->setLayout ("preview");
                $this->view->fullpage = true;
            } else {
                $this->disableLayout ();
                $this->view->fullpage = false;
            }
            
            $this->view->arbre = $metierArbre->getArbre ();
            $this->view->mquestion = $metierQuestion;
        }
    }