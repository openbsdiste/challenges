<?php
    class Challenge_ElaborationController extends App_Controller_Challenge {
        public function init () {
            parent::init ();
            if (
                ($this->_role != Model_Enum_Roles::MODERATEUR)
                || ($this->_chalInfos ['organisateur'] != $this->_identity->id)
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
            if ($this->_chalInfos ['statut'] == Model_Enum_StatutChallenge::ELABORATION) {
                $this->redirect ('challenge/elaboration/accueilelaboration');
            } else {
                $this->redirect ('challenge/elaboration/accueilencours');
            }
        }

        public function accueilelaborationAction () {
            $this->disableLayout ();
            $this->view->challenge = $this->_chalInfos;
        }

        public function accueilencoursAction () {
            $this->disableLayout ();
            $this->view->challenge = $this->_chalInfos;
        }

        public function questionAction () {
            $id = substr ($this->getParam ('id', "node_0"), 5);

            $metierQuestion = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);

            $metierStatuts = new Challenge_Model_Metier_Statuts ();
            $mapperArbre = new Challenge_Model_Mapper_Arbre ();

            $this->disableLayout ();
            $this->view->id = $id;
            $this->view->questionData = $metierQuestion->getQuestion ($id);
            $this->view->questionFiles = $metierQuestion->getFichiers ($id);
            $this->view->reponseData = $metierReponses->getReponse ($id);
            $this->view->reponseFiles = $metierReponses->getFichiers ($id);
            $this->view->statut = $this->_chalInfos ['statut'];
            $this->view->statuts = $metierStatuts->getAllStatuts ();
            $this->view->leaf = $mapperArbre->getNode ($id, $this->_chalInfos ['id']);
        }

        public function imageAction () {
            $id = $this->getParam ('id', "0");

            $metier = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->image = $metier->getImage ($id);
        }

        public function fichierAction () {
            $id = $this->getParam ('id', "0");
            $nom = urldecode ($this->getParam ('nom', ""));
            
            $metier = new Challenge_Model_Metier_Questions ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->fichier = $metier->getFile ($id, $nom);
            $this->view->nom = $nom;
        }

        public function fichierreponseAction () {
            $id = $this->getParam ('id', "0");
            $nom = urldecode ($this->getParam ('nom', ""));

            $metier = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);

            $this->disableLayout ();
            $this->view->fichier = $metier->getFile ($id, $nom);
            $this->view->nom = $nom;
        }

        public function completAction () {
            $fullpage = $this->getParam ('fullpage', false);

            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);

            if ($fullpage) {
                $this->_helper->layout->setLayout ("preview");
                $this->view->fullpage = true;
            } else {
                $this->disableLayout ();
                $this->view->fullpage = false;
            }

            $this->view->arbre = $metierArbre->getArbre ();
            $this->view->chalInfos = $this->_chalInfos;
            $this->view->mquest = new Challenge_Model_Metier_Questions ($this->_chalInfos);
        }

        public function modpubchalAction () {
            $metier = new Model_Metier_Challenges ();

            $this->disableLayout ();
            $this->view->estValide = $metier->testeValiditeChallenge ($this->_chalInfos);
        }

        public function modpubchalconfirmeAction () {
            $this->disableLayout ();
        }

        public function modverchalAction () {
            $metier = new Model_Metier_Challenges ();

            $this->disableLayout ();
            $this->view->stats = $metier->verifierChallenge ($this->_chalInfos);
        }

        public function modvoirstatAction () {
            $metier = new Model_Metier_Challenges ();

            $this->disableLayout ();
            $this->view->stats = $metier->getStatsChallenge ($this->_chalInfos);
        }

        public function modclochalAction () {
            $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);
            $liste = $metierParticipants->getStatutsParticipants ();

            $this->disableLayout ();
            $this->view->liste = $liste;
        }

        public function modclochalconfirmeAction () {
            $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);
            $metierParticipants->unsetBadStatutParticipant ();
            $this->_chalInfos ['statut'] = Model_Enum_StatutChallenge::TERMINE;
            $this->_metierChallenge->setChallenge ($this->_chalInfos);
            $this->redirect ('challenge/index/index');
        }

        public function modstatutsAction () {
            $this->disableLayout ();
        }
        
        public function modlimiteAction () {
            $this->disableLayout ();
            $this->view->challenge = $this->_chalInfos;
        }

        public function testAction () {
            $fichier = '/mnt/partage/www/challenge/public/bulletin.xls';
            $excel = new PHPExcel ();
            unset ($excel);
            $reader = PHPExcel_IOFactory::createReader ('Excel5');
            $obj = $reader->load ($fichier);
            $sheet = $obj->getSheet (0);
            $highestRow = $sheet->getHighestRow(); // 79
            $highestColumn = $sheet->getHighestColumn(); // F
            var_dump ($obj->getProperties ()->getTitle ()); // Challenge 2014
            var_dump ($obj->getProperties ()->getCreator ()); // Libre en poche
            var_dump ($obj->getProperties ()->getSubject ()); // Document de réponse au challenge
            var_dump ($highestColumn, $highestRow);
            var_dump ($sheet->getCell ('A1')->getValue ()); // 32 (id du club)
            for ($i = 6; $i <= $highestRow; $i++) {
                $question = $sheet->getCell ('B' . $i)->getValue ();
                $valeur = $sheet->getCell ('C' . $i)->getValue ();
                $titre = $sheet->getCell ('D' . $i)->getValue ();
                $reponse = $sheet->getCell ('E' . $i)->getValue ();
                var_dump ("$question $valeur $titre $reponse");
            }
            die ();
        }
    }