<?php
    class Challenge_Model_Metier_Zip {
        protected $_chalInfos;
        protected $_identity;
        protected $_id;
        protected $_zip;
        protected $_texte;
        protected $_club;
        
        protected function _initTexte ($nomParticipant) {
            $this->_texte = 
                '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">' . "\n"
                . "<head>\n"
                . '<meta http-equiv="Content-Language" content="fr" />' . "\n"
                . "<title>Challenges Confédération Française Microtel Multimédia</title>\n"
                . '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />' . "\n"
                . "</head>\n"
                . "<html>\n"
                . '<h1>'
                . '<center style="font-size: 1.5em;">'
                . 'Challenge ' . $this->_chalInfos ['annee'] . ' - Réponse<br />'
                . '</center>'
                . '<center style="font-size: 1em;">'
                . '<br />' . $nomParticipant
                . '</center>'
                . '</h1><br />'
                . "\n";
        }
        
        protected function _reponse ($mrep, $reponse, $fichiers, $numQuestion) {
            $documents = "";
            sort ($fichiers);
            $first = true;
            $prefixe = $this->_club . " - " . $numQuestion . " - ";

            foreach ($fichiers as $fichier) {
                $documents .= "<a target='_blank' href='" . $numQuestion . "/$prefixe$fichier'>$prefixe$fichier</a>&nbsp;";
                if ($first) {
                    $first = false;
                    $this->_zip->addEmptyDir ($this->_club . "/" . $numQuestion);
                }
                $fileContent = $mrep->getFile ($reponse ['id'], $fichier, true);
                $this->_zip->addFromString ($this->_club . "/" . $numQuestion . "/$prefixe$fichier", $fileContent);
            }
            if ($documents != '') {
                $documents = "<p>&nbsp;<b>Document(s) : </b>" . $documents . "</p>\n";
            }
            $saisie = $reponse ['texte'];

            $texte = "$documents<div>$saisie</div>\n";
            $this->_texte .= $texte;
        }
        
        protected function _setReponse () {
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierUtilisateurs = new Authentification_Model_Metier_Utilisateurs ();
            $utilisateur = $metierUtilisateurs->trouve ($this->_id);

            $arbre = $metierArbre->getArbre ();
            $nomParticipant = $utilisateur->club;
            $mrep = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $this->_id);

            $this->_initTexte ($nomParticipant);
            // $this->_club = $nomParticipant;
            $this->_club = $utilisateur->login;

            $this->_zip->addEmptyDir ($this->_club);

            $indices = array ();
            $last = 0;
            foreach ($arbre as $feuille) {
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
                $numQuestion = implode ('.', $indices);
                $this->_texte .= "<br /><h1>" . $numQuestion . '. ' . $feuille ['title'] . "</h1><br />\n";
                $r = $mrep->getReponse ($feuille ['noeud']);
                $f = $mrep->getFichiers ($feuille ['noeud']);
                $this->_reponse ($mrep, $r, $f, $numQuestion);
            }
            $this->_zip->addFromString ($this->_club . '/' . $this->_club . ' - reponse.html', $this->_texte . "</html>");
        }
        
        public function __construct ($chalInfos, $identity, $id) {
            $this->_chalInfos = $chalInfos;
            $this->_identity = $identity;
            $this->_id = $id;
        }
        
        public function getZip () {
            $nom = '/tmp/' . uniqid () . '.zip';
            $this->_zip = new ZipArchive ();
            $this->_zip->open ($nom, ZipArchive::CREATE);
            $this->_setReponse ();
            $this->_zip->close ();
            return $nom;
        }
    }