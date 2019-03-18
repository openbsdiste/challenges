<?php
    class Challenge_ReponseController extends App_Controller_Challenge {
        public function init () {
            parent::init ();
            if (
                ($this->_role != Model_Enum_Roles::UTILISATEUR)
                && ($this->_chalInfos ['organisateur'] == $this->_identity->id)
            ) {
                $this->redirect ('/challenge/index/interdit');
            }
            if ($this->_identity->password == md5 ($this->_identity->login)) {
                $this->redirect ('authentification/motdepasse/index');
            }
        }

        public function indexAction () {
        }
        
        public function accueilAction () {
            $this->disableLayout ();
            $this->view->challenge = $this->_chalInfos;
        }
        
        public function questionAction () {
            $id = substr ($this->getParam ('id', "node_0"), 5);

            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);

            $this->disableLayout ();
            $this->view->id = $id;
            $this->view->questionData = $metierQuestions->getQuestion ($id);
            $this->view->questionFiles = $metierQuestions->getFichiers ($id);
            $this->view->reponseData = $metierReponses->getReponse ($id);
            $this->view->reponseFiles = $metierReponses->getFichiers ($id);
            $this->view->statutChallenge = $this->_chalInfos ['statut'];
        }
        
        public function imageAction () {
            $id = $this->getParam ('id', "0");

            $metier = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->image = $metier->getImage ($id);
        }

        public function fichierAction () {
            $id = $this->getParam ('id', "0");
            $nom = $this->getParam ('nom', "");
            
            $metier = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);

            $this->disableLayout ();
            $this->view->fichier = $metier->getFile ($id, $nom);
            $this->view->nom = $nom;
        }
        
        public function fichierreponseAction () {
            $id = $this->getParam ('id', "0");
            $nom = $this->getParam ('nom', "");
            
            $metier = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);

            $this->disableLayout ();
            $this->view->fichier = $metier->getFile ($id, $nom);
            $this->view->nom = $nom;
        }

        public function documentsAction () {
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $this->disableLayout ();
            $this->view->arbre = $metierArbre->getArbre ();
            $this->view->chalInfos = $this->_chalInfos;
            $this->view->mquest = $metierQuestions;
        }
        
        public function impreponsesAction () {
            $fullpage = $this->getParam ('fullpage', false);
            $id = $this->_identity->id;
            
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

        public function modvalchalAction () {
            $this->disableLayout ();
        }
        
        public function modvalchalconfirmeAction () {
            $this->disableLayout ();
        }
        
        public function modverchalAction () {
            $metier = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);
            list ($total, $nbrep, $liste, $totalPoints, $totalPossibles) = $metier->verchal ();

            $this->disableLayout ();
            $this->view->total = $total;
            $this->view->nbrep = $nbrep;
            $this->view->liste = $liste;
            $this->view->totalPoints = $totalPoints;
            $this->view->totalPossibles = $totalPossibles;
        }
        
        public function xlsAction () {
            $this->disableLayout ();
            $excel = new Challenge_Model_Metier_Excelwriter ($this->_chalInfos, $this->_identity);
            $excel->setChallenge ();
            $this->view->excel = $excel;
        }
        
        public function xlsxAction () {
            $this->disableLayout ();
            $excel = new Challenge_Model_Metier_Excelwriter ($this->_chalInfos, $this->_identity);
            $excel->setChallenge ();
            $this->view->excel = $excel;
        }
    }