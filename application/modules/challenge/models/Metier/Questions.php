<?php
    class Challenge_Model_Metier_Questions extends App_Model_Metier {
        protected $_mapperQuestions;
        protected $_challenge;
        protected $_identite;

        protected function _getBaseDir ($id) {
            $dir = implode ('/', array (
                DATA_PATH,
                $this->_challenge ['annee'],
                'questions',
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

        public function __construct ($challenge) {
            $this->_mapperQuestions = new Challenge_Model_Mapper_Questions ();
            $this->_challenge = $challenge;
            $this->_identite = Zend_Auth::getInstance ()->getIdentity ();
        }

        public function getQuestion ($id) {
            $question = $this->_mapperQuestions->findArray (array ('id' => $id, 'challenge' => $this->_challenge ['id']));
            if (! empty ($question)) {
                $question = $question [0];
                if ($question->crypte) {
                    $question->texte = App_Crypto::decrypte ($question->texte, $this->_identite->cle, $this->_challenge ['id']);
                }
            } else {
                $question = new Challenge_Model_Questions ();
                $question->texte = '';
                $question->valeur = '';
                $question->id = $id;
                $question->challenge = $this->_challenge ['id'];
                $question->image = '';
                $question->auteur = '';
                $question->crypte = ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::ELABORATION) ? 1 : 0;
            }
            $questionArray = $this->_mapperQuestions->toArray ($question);
            return $questionArray [0];
        }

        public function getQuestionsId () {
            $liste = $this->_mapperQuestions->findArray (array ('challenge' => $this->_challenge ['id']));
            $ids = array ();
            foreach ($liste as $question) {
                $ids [] = $question->id;
            }
            return $ids;
        }

        public function setQuestion ($data) {
            $question = new Challenge_Model_Questions ($data);
            if ($question->crypte) {
                $question->texte = App_Crypto::encrypte ($question->texte, $this->_identite->cle, $this->_challenge ['id']);
            }
            $this->_mapperQuestions->save ($question);
        }

        public function unsetQuestion ($id) {
            $this->_mapperQuestions->delete (array ('id' => $id, 'challenge' => $this->_challenge ['id']));
            $this->unsetQuestionFiles ($id);
        }

        public function unsetQuestions ($ids) {
            if (! is_array ($ids)) $ids = array ($ids);
            foreach ($ids as $id) {
                $this->unsetQuestion ($id);
            }
        }

        public function getListeAuteurs () {
            $liste = $this->_mapperQuestions->findArray (array ('challenge' => $this->_challenge ['id']));
            $auteurs = array ();
            foreach ($liste as $question) {
                $auteur = strtoupper ($question->auteur);
                if ($auteur != '') {
                    $auteurs [$auteur] = $question->auteur;
                }
            }
            return $auteurs;
        }

        public function getListeQuestionsAuteur ($auteur) {
            $liste = $this->_mapperQuestions->findArray (array ('challenge' => $this->_challenge ['id'], 'auteur' => $auteur));
            $mapperTree = new Challenge_Model_Metier_Arbre ($this->_challenge ['id']);
            $arbre = $mapperTree->getArbre ();
            $questions = array ();
            foreach ($liste as $question) {
                if (isset ($arbre [$question->id])) {
                    $questions [$question->id] = $arbre [$question->id]['title'];
                }
            }
            return $questions;
        }

        public function getListeQuestionsStatut ($statut) {
            $liste = $this->_mapperQuestions->findArray (array ('challenge' => $this->_challenge ['id'], 'statut' => $statut));
            $mapperTree = new Challenge_Model_Metier_Arbre ($this->_challenge ['id']);
            $arbre = $mapperTree->getArbre ();
            $questions = array ();
            foreach ($liste as $question) {
                if (isset ($arbre [$question->id])) {
                    $questions [$question->id] = $arbre [$question->id]['title'];
                }
            }
            return $questions;
        }

        public function getFile ($question, $nom) {
            $dir = $this->_getBaseDir ($question) . $nom;
            if (is_file ($dir)) {
                $file = file_get_contents ($dir);
                if ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::ELABORATION) {
                    $file = App_Crypto::decrypte ($file, $this->_identite->cle, $this->_challenge ['id']);
                }
            } else {
                $file = false;
            }
            return $file;
        }

        public function setFile ($question, $nom, $contenu, $forceClair = false) {
            $dir = $this->_getBaseDir ($question);
            $this->_createDirIfNotExist ($dir);
            $filename = $dir . $nom;
            if (! $forceClair && ($this->_challenge ['statut'] == Model_Enum_StatutChallenge::ELABORATION)) {
                $contenu = App_Crypto::encrypte ($contenu, $this->_identite->cle, $this->_challenge ['id']);
            }
            file_put_contents ($filename, $contenu);
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

        public function unsetQuestionFiles ($question) {
            $dir = $this->_getBaseDir ($question);
            $this->deleteDir ($dir);
        }

        public function getImage ($question) {
            $q = $this->getQuestion ($question);
            if ($q ['image'] != '') {
                $image = array (
                    'nom' => $q ['image'],
                    'contenu' => $this->getFile ($question, $q ['image'])
                );
            } else {
                $image = array (
                    'nom' => 'no-image.png',
                    'contenu' => file_get_contents (PUBLIC_PATH . '/mediatheque/images/no-image.png')
                );
            }
            return $image;
        }

        public function getFichiers ($question) {
            $dir = $this->_getBaseDir ($question);
            $q = $this->getQuestion ($question);
            $fichiers = array ();
            if (is_dir ($dir)) {
                foreach (new DirectoryIterator ($dir) as $f) {
                    if ($f->isFile () && ($f->getFilename () != $q ['image'])) {
                        $fichiers [] = $f->getFilename ();
                    }
                }
            }
            sort ($fichiers);
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

        public function getCorrectionQuestion ($qid) {
            $metierReponse = new Challenge_Model_Metier_Reponses ($this->_challenge, $this->_challenge ['organisateur']);
            return array (
                $metierReponse->getReponse ($qid),
                $metierReponse->getFichiers ($qid)
            );
        }
    }
