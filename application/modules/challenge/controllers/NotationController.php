<?php
    class Challenge_NotationController extends App_Controller_Challenge {
        public function init () {
            parent::init ();
            if (
                ($this->_role != Model_Enum_Roles::MODERATEUR)
                || ($this->_chalInfos ['organisateur'] != $this->_identity->id)
                || ($this->_chalInfos ['statut'] != Model_Enum_StatutChallenge::TERMINE)
            ) {
                $this->redirect ('challenge/index/interdit');
            }
        }

        public function indexAction () {
        }

        public function accueilAction () {
            $this->view->challenge = $this->_chalInfos;
            $this->disableLayout ();
        }

        public function questionAction () {
            $id = substr ($this->getParam ('id', "node_0"), 5);

            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_identity->id);
            $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->id = $id;
            $this->view->challengeStatut = $this->_chalInfos ['statut'];
            $this->view->questionData = $metierQuestions->getQuestion ($id);
            $this->view->questionFiles = $metierQuestions->getFichiers ($id);
            $this->view->reponseData = $metierReponses->getReponse ($id);
            $this->view->reponseFiles = $metierReponses->getFichiers ($id);
            $this->view->reponses = $metierParticipants->getFestelParticipantsId ($id);
        }

        public function fichierAction () {
            $id = $this->getParam ('q', 0);
            $fic = urldecode ($this->getParam ('fic', ''));
            $club = intval (App_Crypto::festelFloat ($this->getParam ('club', 0), $id));

            $metierReponse = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $club);

            $this->disableLayout ();
            $this->view->fichier = $metierReponse->getFile ($id, $fic, true);
            $this->view->nom = $fic;
        }

        public function xlsAction () {
            $this->disableLayout ();
            $excel = new Challenge_Model_Metier_Noteswriter ($this->_chalInfos, $this->_identity);
            $excel->setChallenge ();
            $this->view->excel = $excel;
        }

        public function xlsxAction () {
            $this->disableLayout ();
            $excel = new Challenge_Model_Metier_Noteswriter ($this->_chalInfos, $this->_identity);
            $excel->setChallenge ();
            $this->view->excel = $excel;
        }

        public function zipAction () {
            $this->disableLayout ();
            $id = $this->getParam ('numero', "0");
            $metier = new Challenge_Model_Metier_Zip ($this->_chalInfos, $this->_identity, $id);
            $zip = $metier->getZip ();
            $this->view->zip = $zip;
            $this->view->id = $id;
        }

        public function modexpchalAction () {
            $metier = new Challenge_Model_Metier_Participants ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->clubs = $metier->getNomsParticipants ();
        }

        public function modverchalAction () {
            $challenge = new Model_Metier_Challenges ();

            $this->disableLayout ();
            $this->view->liste = $challenge->getStatsNotationChallenge ($this->_chalInfos);
        }

        public function modreschalAction () {
            $metier = new Challenge_Model_Metier_Participants ($this->_chalInfos);

            $this->disableLayout ();
            $this->view->liste = $metier->getResultats ();
        }

        public function modterchalAction () {
            $this->disableLayout ();
        }

        public function modnotmanAction () {
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();

            $this->disableLayout ();
            $liste = $metierUtilisateurs->getListeClubs ();
            // Le modérateur ne participe pas au challenge, on le supprime de la liste !
            unset ($liste [$this->_identity->id]);
            $this->view->clubs = $liste;
        }

        public function modimpchalAction () {
            $this->disableLayout ();
        }

        public function modexpnotAction () {
            $this->disableLayout ();
        }
    }
