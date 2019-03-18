<?php
    class Challenge_Model_Metier_Reponses extends App_Model_Metier {
        protected $_mapperReponses;
        protected $_metierParticipants;
        protected $_participant;
        protected $_challenge;
        protected $_identite;
        protected $_clubId;
        
        protected function _getBaseDir ($id) {
            $dir = implode ('/', array (
                DATA_PATH,
                $this->_challenge ['annee'],
                'reponses',
                $this->_clubId,
                $id
            ));
            return str_replace ("\\", "/", $dir) . "/";
        }
        
        protected function _createDirIfNotExist ($dir) {
            if (! is_dir ($dir)) {
                $this->_createDirIfNotExist (dirname ($dir));
                @mkdir ($dir);
            }
        }
        
        public function __construct ($challenge, $clubId) {
            $this->_mapperReponses = new Challenge_Model_Mapper_Reponses ();
            $this->_metierParticipants = new Challenge_Model_Metier_Participants ($challenge);
            $this->_participant = $this->_metierParticipants->getParticipant ($clubId);
            $this->_challenge = $challenge;
            $this->_clubId = $clubId;
            $this->_identite = Zend_Auth::getInstance ()->getIdentity ();
        }
        
        public function getReponse ($id) {
            $reponse = $this->_mapperReponses->findArray (array ('id' => $id, 'challenge' => $this->_challenge ['id'], 'club' => $this->_clubId));
            if (! empty ($reponse)) {
                $reponse = $reponse [0];
                if ($reponse->crypte) {
                    $reponse->texte = App_Crypto::decrypte ($reponse->texte, $this->_identite->cle);
                    $reponse->notesinternes = App_Crypto::decrypte ($reponse->notesinternes, $this->_identite->cle);
                } elseif ($reponse->moderation) {
                    if ($this->_challenge ['organisateur'] == $this->_clubId) {
                        $cle = $this->_identite->password;
                    } else {
                        $mapperm = new Authentification_Model_Mapper_Utilisateurs ();
                        $moderateur = $mapperm->find ($this->_challenge ['organisateur']);
                        $cle = $moderateur->password;
                    }
                    $reponse->texte = App_Crypto::decrypte ($reponse->texte, $cle);
                    $reponse->notesinternes = App_Crypto::decrypte ($reponse->notesinternes, $cle);
                }
                if ($reponse->note != -1) {
                    $reponse->note = App_Crypto::festelFloat ($reponse->note, $reponse->club);
                }
            } else {
                $reponse = new Challenge_Model_Reponses ();
                $reponse->challenge = $this->_challenge ['id'];
                $reponse->club = $this->_clubId;
                $reponse->id = $id;
                $reponse->crypte = ($this->_challenge ['statut'] <= Model_Enum_StatutChallenge::ENCOURS) ? 1 : 0;
                $reponse->moderation = 0;
                $reponse->note = -1;
                $reponse->notesinternes = '';
                $reponse->texte = '';
            }
            $reponseArray = $this->_mapperReponses->toArray ($reponse);
            return $reponseArray [0];
        }
        
        public function getReponsesId () {
            $liste = $this->_mapperReponses->findArray (array ('challenge' => $this->_challenge ['id'], 'club' => $this->_clubId));
            $ids = array ();
            foreach ($liste as $reponse) {
                $ids [] = $reponse->id;
            }
            return $ids;
        }

        public function getNotesClub () {
            $liste = $this->_mapperReponses->findArray (array ('challenge' => $this->_challenge ['id'], 'club' => $this->_clubId));
            $notes = array ();
            foreach ($liste as $reponse) {
                if ($reponse->note != -1) {
                    $notes [$reponse->id] = App_Crypto::festelFloat ($reponse->note, $reponse->club);
                }
            }
            return $notes;
        }
        
        public function setReponse ($data) {
            $reponse = new Challenge_Model_Reponses ($data);
            if ($reponse->crypte) {
                $reponse->texte = App_Crypto::encrypte ($reponse->texte, $this->_identite->cle);
                $reponse->notesinternes = App_Crypto::encrypte ($reponse->notesinternes, $this->_identite->cle);
            } elseif ($reponse->moderation) {
                if ($this->_challenge ['organisateur'] == $this->_clubId) {
                    $cle = $this->_identite->password;
                } else {
                    $mapperm = new Authentification_Model_Mapper_Utilisateurs ();
                    $moderateur = $mapperm->find ($this->_challenge ['organisateur']);
                    $cle = $moderateur->password;
                }
                $reponse->texte = App_Crypto::encrypte ($reponse->texte, $cle);
                $reponse->notesinternes = App_Crypto::encrypte ($reponse->notesinternes, $cle);
            }
            if ($reponse->note != -1) {
                $reponse->note = App_Crypto::festelFloat ($reponse->note, $reponse->club);
            }
            $this->_mapperReponses->save ($reponse);
            if ($reponse->crypte) {
                $this->_metierParticipants->setParticipant ($this->_participant);
            }
        }
        
        public function unsetReponse ($id) {
            $this->_mapperReponses->delete (array ('id' => $id, 'challenge' => $this->_challenge ['id'], 'club' => $this->_clubId));
            $this->unsetReponseFiles ($id);
        }
        
        public function getFile ($question, $nom, $force = false) {
            $dir = $this->_getBaseDir ($question) . $nom;
            if (is_file ($dir)) {
                $file = file_get_contents ($dir);
                if (! $this->_participant ['valide']) {
                    $file = App_Crypto::decrypte ($file, $this->_identite->cle);
                } elseif ($force) {
                    $mapper = new Authentification_Model_Mapper_Utilisateurs ();
                    $moderateur = $mapper->find ($this->_challenge ['organisateur']);
                    $file = App_Crypto::decrypte ($file, $moderateur->password);
                }
            } else {
                $file = false;
            }
            return $file;
        }
        
        public function setFile ($question, $nom, $contenu, $force = false) {
            $dir = $this->_getBaseDir ($question);
            $this->_createDirIfNotExist ($dir);
            $filename = $dir . $nom;
            if (! $force && ! $this->_participant ['valide']) {
                $contenu = App_Crypto::encrypte ($contenu, $this->_identite->cle);
            } elseif ($force && $this->_challenge ['organisateur'] != $this->_clubId) {
//            } elseif ($force) {
                $mapper = new Authentification_Model_Mapper_Utilisateurs ();
                $moderateur = $mapper->find ($this->_challenge ['organisateur']);
                $contenu = App_Crypto::encrypte ($contenu, $moderateur->password);
            }
            file_put_contents ($filename, $contenu);
            $this->_metierParticipants->setParticipant ($this->_participant);
        }
        
        public function unsetFile ($question, $nom) {
            $dir = $this->_getBaseDir ($question);
            if (is_file ($dir . $nom)) {
                unlink ($dir . $nom);
            }
        }
        
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

        public function unsetReponseFiles ($question) {
            $dir = $this->_getBaseDir ($question);
            $this->deleteDir ($dir);
        }
        
        public function getFichiers ($question) {
            $dir = $this->_getBaseDir ($question);

            $fichiers = array ();
            if (is_dir ($dir)) {
                foreach (new DirectoryIterator ($dir) as $f) {
                    if ($f->isFile ()) {
                        $fichiers [] = $f->getFilename ();
                    }
                }
            }
            return $fichiers;
        }
        
        public function getListeFichiers () {
            $dir = substr ($this->_getBaseDir (''), 0, -1);
            $liste = array ();
            if (is_dir ($dir)) {
                foreach (new DirectoryIterator ($dir) as $q) {
                    foreach (new DirectoryIterator ($dir . $q->getFilename ()) as $f) {
                        if ($f->isFile ()) {
                            $liste [] = array (
                                'q' => $q->getFilename (),
                                'nom' => $f->getFilename ()
                            );
                        }
                    }
                }
            }
            return $liste;
        }
        
        public function verchal () {
            $mapperArbre = new Challenge_Model_Mapper_Arbre ();
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_challenge ['id']);
            $arbre = $mapperArbre->getChildren (1, $this->_challenge ['id'], true);
            $liste = array ();
            $total = $nbrep = $nbfic = 0;
            $totalPoints = $totalPossibles = 0;
            $indices = array ();
            $last = 0;
            foreach ($arbre as $feuille) {
                $question = $metierQuestions->getQuestion ($feuille ['noeud']);
                
                if ($last < $feuille ['level']) {
                    $indices [] = 0;
                } elseif ($last > $feuille ['level']) {
                    while ($last > $feuille ['level']) {
                        $last--;
                        array_pop ($indices);
                    }
                }
                $val = array_pop ($indices);
                $indices [] = $val + 1;
                $last = $feuille ['level'];
                
                if (! $question ['information']) {
                    $total++;
                    $reponse = $this->getReponse ($feuille ['noeud']);
                    $fichiers = $this->getFichiers ($feuille ['noeud']);
                    $info = array (
                        'titre' => $feuille ['title'],
                        'valeur' => $question ['valeur'],
                        'fichier' => (empty ($fichiers)) ? 'bad' : 'good',
                        'numero' => implode ('.', $indices) . '. '
                    );
                    $totalPossibles += $question ['valeur'];
                    if ($reponse ['texte'] == '') {
                        $info ['reponse'] = 'bad';
                        if (! empty ($fichiers)) {
                            $nbrep++;
                        }
                    } else {
                        $info ['reponse'] = 'good';
                        $nbrep++;
                    }
                    if (($info ['reponse'] == 'good') || ($info ['fichier'] == 'good')) {
                        $totalPoints += $question ['valeur'];
                    }
                    $liste [] = $info;
                }
            }
            return array ($total, $nbrep, $liste, $totalPoints, $totalPossibles);
        }
        
        public function haveReponse ($qid) {
            $have = false;
            $reponse = $this->getReponse ($qid);
            $fichiers = $this->getFichiers ($qid);
            if (($reponse ['texte'] != '') || ! empty ($fichiers)) {
                $have = true;
            }
            return $have;
        }
    }