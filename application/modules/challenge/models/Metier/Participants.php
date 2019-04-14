<?php
    class Challenge_Model_Metier_Participants extends App_Model_Metier {
        protected $_mapperParticipants;
        protected $_challenge;
        protected $_identite;
        
        public function deleteDir ($path) {
            if (is_dir ($path)) {
                if (substr ($path, strlen ($path) - 1, 1) != '/') {
                    $path .= '/';
                }
                $dotfiles = glob ($path . '.*', GLOB_MARK);
                $files = glob ($path . '*', GLOB_MARK);
                $files = array_merge ($files, $dotfiles);
                foreach ($files as $file) {
                    if (basename ($file) == '.' || basename ($file) == '..') {
                        continue;
                    } else if (is_dir ($file)) {
                        self::deleteDir ($file);
                    } else {
                        unlink ($file);
                    }
                }
                rmdir ($path);
            }
        }

        public function __construct ($challenge) {
            $this->_mapperParticipants = new Challenge_Model_Mapper_Participants ();
            $this->_challenge = $challenge;
            $this->_identite = Zend_Auth::getInstance ()->getIdentity ();
        }
        
        public function getParticipant ($club) {
            $participant = $this->_mapperParticipants->findArray (array ('club' => $club, 'challenge' => $this->_challenge ['id']));
            if (! empty ($participant)) {
                $participant = $participant [0];
                if ($participant->calculee != -1) {
                    $participant->calculee = App_Crypto::festelFloat ($participant->calculee, $participant->club);
                }
                if ($participant->attribuee != -1) {
                    $participant->attribuee = App_Crypto::festelFloat ($participant->attribuee, $participant->club);
                }
            } else {
                $participant = new Challenge_Model_Participants ();
                $participant->challenge = $this->_challenge ['id'];
                $participant->club = $this->_identite->id;
                $participant->valide = ($this->_challenge ['statut'] <= Model_Enum_StatutChallenge::ENCOURS) ? 0 : 1;
                $participant->calculee = -1;
                $participant->attribuee = -1;
                $participant->password = '';
            }
            $participantArray = $this->_mapperParticipants->toArray ($participant);
            return $participantArray [0];
        }

        public function setParticipant ($data) {
            $participant = new Challenge_Model_Participants ($data);
            if (
                    (
                        ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::ELABORATION)
                        && ($this->_challenge ['organisateur'] == $participant->club)
                    )
                    || ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::ENCOURS)
                    || ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::TERMINE)
            ) {
                if ($participant->calculee != -1) {
                    $participant->calculee = App_Crypto::festelFloat ($participant->calculee, $participant->club);
                }
                if ($participant->attribuee != -1) {
                    $participant->attribuee = App_Crypto::festelFloat ($participant->attribuee, $participant->club);
                }
                $this->_mapperParticipants->save ($participant);
            }
        }
        
        public function getParticipants () {
            return $this->_mapperParticipants->findArray (array ('challenge' => $this->_challenge ['id']));
        }
        
        public function getNonParticipants () {
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $clubs = $metierUtilisateurs->getListeClubs ();
            $participants = $this->getParticipants ();
            foreach ($participants as $participant) {
                if (isset ($clubs [$participant->club])) {
                    unset ($clubs [$participant->club]);
                }
            }
            return $clubs;
        }
        
        public function getNomsParticipants () {
            $liste = array ();
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $clubs = $metierUtilisateurs->getListeClubs ();
            $participants = $this->getParticipants ();
            foreach ($participants as $participant) {
                if (isset ($clubs [$participant->club])) {
                    $liste [$participant->club] = $clubs [$participant->club];
                }
            }
            // On ajoute le club organisateur...
            $liste [$this->_identite->id] = $this->_identite->club;
            return $liste;
        }

        public function isParticipant ($clubId) {
            $participants = $this->getParticipants ();
            foreach ($participants as $participant) {
                if ($participant->club == $clubId) {
                    return true;
                }
            }
            return false;
        }
        
        public function getStatutsParticipants () {
            $participants = $this->getParticipants ();
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $liste = array ();
            foreach ($participants as $participant) {
                if ($participant->club != $this->_identite->id) {
                    $user = $metierUtilisateurs->trouve ($participant->club);
                    $liste [] = array (
                        'club' => $user->club,
                        'valide' => ($participant->valide) ? 'good': 'bad',
                        'id' => $user->id
                    );
                }
            }
            return $liste;
        }
        
        public function getFestelParticipantsId ($questionId) {
            $reponses = array ();
            $i = 1;
            foreach ($this->getParticipants () as $participant) {
                $metierReponse = new Challenge_Model_Metier_Reponses ($this->_challenge, $participant->club);
                if ($metierReponse->haveReponse ($questionId)) {
                    if ($participant->club == $this->_challenge ['organisateur']) {
                        $reponses ['Correction'] = App_Crypto::festelFloat ($participant->club, $questionId);
                    } else {
                        $reponses [$i++] = App_Crypto::festelFloat ($participant->club, $questionId);
                    }
                }
            }
            return $reponses;
        }
        
        public function unsetBadStatutParticipant () {
            $participants = $this->getParticipants ();
            foreach ($participants as $participant) {
                if (! $participant->valide && ($participant->club != $this->_identite->id)) {
                    $this->supprimeParticipant ($participant->club);
                }
            }
        }
        
        public function getParticipantsEnCours () {
            return $this->_mapperParticipants->findArray (array ('challenge' => $this->_challenge ['id'], 'valide' => 0));
        }

        public function getParticipantsValides () {
            return $this->_mapperParticipants->findArray (array ('challenge' => $this->_challenge ['id'], 'valide' => 1));
        }
        
        public function supprimeParticipant ($id) {
            $dir = str_replace ("\\", "/", implode ('/', array (
                DATA_PATH,
                $this->_challenge ['annee'],
                'reponses',
                $id
            )));
            $this->deleteDir ($dir);
            $mapperReponses = new Challenge_Model_Mapper_Reponses ();
            $mapperReponses->delete (array (
                'club' => $id,
                'challenge' => $this->_challenge ['id']
            ));
            $this->_mapperParticipants->delete (array (
                'club' => $id,
                'challenge' => $this->_challenge ['id']
            ));
        }
        
        public static function trieResultat ($r1, $r2) {
            if ($r1 ['note'] == $r2 ['note']) {
                return 0;
            }
            return (floatval ($r1 ['note']) > floatval ($r2 ['note'])) ? -1 : +1;
        }
        
        public function getResultats () {
            $liste = array ();
            $participants = $this->getParticipants ();
            $mapper = new Authentification_Model_Mapper_Utilisateurs ();
            foreach ($participants as $participant) {
                if ($participant->club != $this->_challenge ['organisateur']) {
                    if ($participant->calculee != -1) {
                        $participant->calculee = App_Crypto::festelFloat ($participant->calculee, $participant->club);
                    }
                    if ($participant->attribuee != -1) {
                        $participant->attribuee = App_Crypto::festelFloat ($participant->attribuee, $participant->club);
                    }
                    $club = $mapper->find ($participant->club);
                    $liste [] = array (
                        'id' => $participant->club,
                        'club' => $club->club,
                        'note' => $participant->calculee,
                        'bonus' => $participant->attribuee
                    );
                }
            }
            usort ($liste, array ("Challenge_Model_Metier_Participants", "trieResultat"));
            return $liste;
        }
    }
