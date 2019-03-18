<?php
    class Authentification_MotdepasseController extends App_Controller_Action {
        public function init () {
            parent::init ();
            
            $metierChallenge = new Model_Metier_Challenges ();
            $challenges = $metierChallenge->getListeChallengesAdmin ();
            foreach ($challenges as $challenge) {
                if (
                    ($challenge->statut == Model_Enum_StatutChallenge::ENCOURS)
                    && ($this->_role == Model_Enum_Roles::UTILISATEUR)
                ) {
                    $metierParticipants = new Challenge_Model_Metier_Participants ($challenge->getDonnees (true));
                    if (
                        $metierParticipants->isParticipant ($this->_identity->id)
                        && ($this->getRequest ()->getActionName () != 'impossible')
                    ) {
                        $this->redirect ('authentification/motdepasse/impossible');
                    }
                } elseif (
                    ($challenge->statut != Model_Enum_StatutChallenge::VALIDE)
                    && ($this->_role == Model_Enum_Roles::MODERATEUR)
                    && ($challenge->organisateur == $this->_identity->id)
                    && ($this->getRequest ()->getActionName () != 'impossible')
                ) {
                    $this->redirect ('authentification/motdepasse/impossible');
                }
            }
        }
        
        public function indexAction () {
            $formulaire = new Authentification_Form_Passe ();
            
            if ($this->_request->isPost ()) {
                if ($formulaire->isValid ($this->_getAllParams ())) {
                    $ancien = $formulaire->getValue ('ancien');
                    $nouveau = $formulaire->getValue ('nouveau');

                    if ($this->_identity->password == md5 ($ancien)) {
                        $memoire = $formulaire->getValue ('memoire');
                        $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
                        $metierUtilisateurs->changePassword ($this->_identity->id, $nouveau, $memoire);
                        $this->_redirect ('authentification/deconnexion/index');
                    } else {
                        $formulaire->getElement ('ancien')->addError ('form.passe.invalide');
                    }
                }
            } else {
                $formulaire->getElement ('memoire')->setValue ($this->_identity->memoire);
            }
            $this->view->formulaire = $formulaire;
        }
        
        public function impossibleAction () {
        }
    }