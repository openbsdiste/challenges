<?php
    class Model_Metier_Challenges extends App_Model_Metier {
        protected $_mapperChallenges;
        protected $_identite;

        public function __construct () {
            $this->_mapperChallenges = new Model_Mapper_Challenges ();
            $this->_identite = Zend_Auth::getInstance ()->getIdentity ();
        }

        public function getListeChallengesAdmin () {
            $liste = $this->_mapperChallenges->fetchAll (null, 'annee DESC');
            if (!is_array ($liste)) $liste = array ($liste);
            return $liste;
        }

        public function getListeChallenges () {
            $liste = array ();
            $challenges = $this->getListeChallengesAdmin ();
            $utilisateurs = new Authentification_Model_Mapper_Utilisateurs ();
            foreach ($challenges as $challenge) {
                $utilisateur = $utilisateurs->find ($challenge->organisateur);
                switch ($challenge->statut) {
                    case Model_Enum_StatutChallenge::ELABORATION: $icone = 'cancel'; break;
                    case Model_Enum_StatutChallenge::ENCOURS: $icone = 'heart'; break;
                    case Model_Enum_StatutChallenge::TERMINE: $icone = 'locked'; break;
                    case Model_Enum_StatutChallenge::VALIDE: $icone = 'check'; break;
                }
                $liste [] = array (
                    'id' => $challenge->id,
                    'icone' => $icone,
                    'annee' => $challenge->annee,
                    'organisateur' => $utilisateur->club
                );
            }
            return $liste;
        }

        public function getChallenge ($id) {
            $chalInfos = false;
            $challenge = $this->_mapperChallenges->find ($id);
            if ($challenge) {
                $mapper = new Authentification_Model_Mapper_Utilisateurs ();
                $utilisateur = $mapper->find ($challenge->organisateur);
                if ($utilisateur) {
                    $chalInfos = array (
                        'id' => $challenge->id,
                        'annee' => $challenge->annee,
                        'statut' => $challenge->statut,
                        'organisateur' => $challenge->organisateur,
                        'club' => $utilisateur->club,
                        'datelimite' => $challenge->datelimite
                    );
                }
            }
            return $chalInfos;
        }

        public function setChallenge ($data) {
            $this->_mapperChallenges->save (new Model_Challenges ($data));
        }

        public function creeChallenge ($valeurs) {
            $challenge = new Model_Challenges ($valeurs);
            $id = $this->_mapperChallenges->save ($challenge);

            $metierUtilisateur = new Authentification_Model_Metier_Utilisateurs ();
            $utilisateur = $metierUtilisateur->trouve ($valeurs ['organisateur']);
            $utilisateur->role = Model_Enum_Roles::MODERATEUR;
            $metierUtilisateur->setUtilisateur ($utilisateur);
        }

        public function publicationCompte ($chalId) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Questions ($challenge);
            return array (
                'questions' => $mapper->getQuestionsId (),
                'documents' => $mapper->getListeFichiers ()
            );
        }

        public function publicationQuestion ($chalId, $qId) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Questions ($challenge);
            $question = $mapper->getQuestion ($qId);
            $question ['crypte'] = 0;
            $mapper->setQuestion ($question);
            return array ();
        }

        public function publicationDocument ($chalId, $qId, $nom) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Questions ($challenge);
            $contenu = $mapper->getFile ($qId, $nom);
            $mapper->setFile ($qId, $nom, $contenu, true);
            return array ();
        }

        public function publicationOuvre ($chalId) {
            $challenge = $this->_mapperChallenges->find ($chalId);
            $challenge->statut = Model_Enum_StatutChallenge::ENCOURS;
            $this->_mapperChallenges->save ($challenge);
            return array ();
        }

        public function validationCompte ($chalId) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Reponses ($challenge, $this->_identite->id);

            return array (
                'questions' => $mapper->getReponsesId (),
                'documents' => $mapper->getListeFichiers ()
            );
        }

        public function validationQuestion ($chalId, $qId) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Reponses ($challenge, $this->_identite->id);
            $question = $mapper->getReponse ($qId);
            $question ['crypte'] = 0;
            $question ['moderation'] = 1;
            $question ['notesinternes'] = '';
            $mapper->setReponse ($question);
            return array ();
        }

        public function validationDocument ($chalId, $qId, $nom) {
            $challenge = $this->getChallenge ($chalId);
            $mapper = new Challenge_Model_Metier_Reponses ($challenge, $this->_identite->id);
            $contenu = $mapper->getFile ($qId, $nom);
            $mapper->setFile ($qId, $nom, $contenu, true);
            return array ();
        }

        public function validationOuvre ($chalId) {
            $challenge = $this->getChallenge ($chalId);
            $metierParticipants = new Challenge_Model_Metier_Participants ($challenge);
            $participant = $metierParticipants->getParticipant ($this->_identite->id);
            $participant ['valide'] = 1;
            $metierParticipants->setParticipant ($participant);
            return array ();
        }

        public function getStatsChallenge ($challenge) {
            $metierParticipants = new Challenge_Model_Metier_Participants ($challenge);
            $encours = $metierParticipants->getParticipantsEnCours ();
            $valides = $metierParticipants->getParticipantsValides ();
            $stats = array (
                'encours' => sizeof ($encours),
                'valides' => sizeof ($valides),
                'questions' => array (),
                'clubs' => array (),
                'liste' => array ()
            );
            $total = array_merge ($encours, $valides);
            $mapperUser = new Authentification_Model_Mapper_Utilisateurs ();
            foreach ($total as $participant) {
                if ($participant->club != $this->_identite->id) {
                    $user = $mapperUser->find ($participant->club);
                    $v = ($participant->valide == 1) ? "*" : "";
                    $stats ['clubs'][] = array ('login' => $user->login . $v, 'club' => $user->club . $v);
                    $metier = new Challenge_Model_Metier_Reponses ($challenge, $participant->club);
                    list ($t, $n, $liste) = $metier->verchal ();
                    if (empty ($stats ['questions'])) {
                        foreach ($liste as $v) $stats ['questions'][] = $v ['titre'];
                    }
                    $okko = array ();
                    foreach ($liste as $v) {
                        if (($v ['reponse'] == 'good') || ($v ['fichier'] == 'good')) {
                            $okko [] = 'good';
                        } else {
                            $okko [] = 'bad';
                        }
                    }
                    $stats ['liste'][] = $okko;
                } else {
                    $stats ['encours']--;
                }
            }
            return $stats;
        }

        public function getStatsNotationChallenge ($challenge) {
            $metierParticipants = new Challenge_Model_Metier_Participants ($challenge);
            $valides = $metierParticipants->getParticipantsValides ();
            $mapperArbre = new Challenge_Model_Mapper_Arbre ();
            $arbre = $mapperArbre->getChildren (1, $challenge ['id'], true);
            $liste = array ();
            foreach ($arbre as $noeud) {
                $liste [$noeud ['noeud']] = array (
                    'id' => $noeud ['noeud'],
                    'titre' => $noeud ['title'],
                    'repchoisie' => true,
                    'havereponse' => false,
                    'ok' => true
                );
            }
            foreach ($valides as $participant) {
                $metier = new Challenge_Model_Metier_Reponses ($challenge, $participant->club);
                foreach ($liste as $id => $question) {
                    if ($metier->haveReponse ($id)) {
                        $liste [$id]['havereponse'] = true;
                        $reponse = $metier->getReponse ($id);
                        $liste[$id]['ok'] = ($question ['ok'] && ($reponse ['note'] >= 0));
                    }
                }
            }
            $metierQuestion = new Challenge_Model_Metier_Questions ($challenge);
            foreach ($liste as $id => $q) {
                $question = $metierQuestion->getQuestion ($id);
                if ($question ['information']) {
                    unset ($liste [$id]);
                } else {
                    if ($q ['havereponse']) {
                        $liste [$id]['repchoisie'] = true;
                    }
                }
            }
            return $liste;
        }

        public function termineChallenge ($challenge) {
            try {
                $chalInfos = $this->getChallenge ($challenge);
                $metierParticipants = new Challenge_Model_Metier_Participants ($chalInfos);
                $valides = $metierParticipants->getParticipantsValides ();
                $mapperArbre = new Challenge_Model_Mapper_Arbre ();
                //$arbre = $mapperArbre->getChildren (1, $challenge ['id'], true);
                $arbre = $mapperArbre->getChildren (1, $challenge, true);
                foreach ($valides as $participant) {
                    $metierReponse = new Challenge_Model_Metier_Reponses ($chalInfos, $participant->club);
                    foreach ($arbre as $noeud) {
                        $id = $noeud ['noeud'];
                        if ($metierReponse->haveReponse ($id)) {
                            $reponse = $metierReponse->getReponse ($id);
                            $fichiers = $metierReponse->getFichiers ($id);
                            $reponse ['moderation'] = 0;
                            $metierReponse->setReponse ($reponse);
                            foreach ($fichiers as $fichier) {
                                $contenu = $metierReponse->getFile ($id, $fichier, true);
                                $metierReponse->setFile ($id, $fichier, $contenu);
                            }
                        }
                    }
                }
                $chalInfos ['statut'] = Model_Enum_StatutChallenge::VALIDE;
                $this->_mapperChallenges->save (new Model_Challenges ($chalInfos));
                $mapperUtilisateurs = new Authentification_Model_Mapper_Utilisateurs ();
                $utilisateur = $mapperUtilisateurs->find ($this->_identite->id);
                $utilisateur->role = Model_Enum_Roles::UTILISATEUR;
                $mapperUtilisateurs->save ($utilisateur);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function verifierChallenge ($chalInfos) {
            $stats = array (
                'nbquestions' => 0,
                'nbinformations' => 0,
                'elements' => array ()
            );
            $mapperArbre = new Challenge_Model_Mapper_Arbre ();
            $arbre = $mapperArbre->getChildren (1, $chalInfos ['id'], true);
            $metierStatuts = new Challenge_Model_Metier_Statuts ();
            $statutsBloquants = $metierStatuts->getStatutsBloquants ();
            $metierQuestions = new Challenge_Model_Metier_Questions ($chalInfos);
            foreach ($arbre as $noeud) {
                $question = $metierQuestions->getQuestion ($noeud ['noeud']);
                $documents = $metierQuestions->getFichiers ($noeud ['noeud']);
                if ($question ['information']) {
                    $stats ['nbinformations']++;
                    $stats ['elements'][] = array (
                        'level' => $noeud ['level'],
                        'titre' => $noeud ['title'],
                        'info' => true,
                        'texte' => ($question ['texte'] == '') ? 'bad' : 'good',
                        'documents' => (! empty ($documents)),
                        'auteur' => $question ['auteur'],
                        'statut' => (in_array ($question ['statut'], $statutsBloquants)) ? 'bad' : 'good'
                    );
                } else {
                    $stats ['nbquestions']++;
                    $stats ['elements'][] = array (
                        'level' => $noeud ['level'],
                        'titre' => $noeud ['title'],
                        'info' => false,
                        'texte' => ($question ['texte'] == '') ? 'bad' : 'good',
                        'documents' => ( !empty ($documents)),
                        'valeur' => ($question ['valeur'] == '') ? 'bad' : 'good',
                        'auteur' => $question ['auteur'],
                        'statut' => (in_array ($question ['statut'], $statutsBloquants)) ? 'bad' : 'good'
                    );
                }
            }
            return $stats;
        }

        public function testeValiditeChallenge ($chalInfos) {
            $stats = $this->verifierChallenge ($chalInfos);
            foreach ($stats ['elements'] as $v) {
                if (
                    ($v ['texte'] == 'bad')
                    || ($v ['statut'] == 'bad')
                    || (isset ($v ['valeur']) && ($v ['valeur'] == 'bad'))
                ) {
                    return false;
                }
            }
            return true;
        }
    }
