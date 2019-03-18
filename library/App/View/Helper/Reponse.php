<?php
    class App_View_Helper_Reponse extends Zend_View_Helper_Abstract {
        public function reponse ($reponse, $fichiers) {
            $documents = "";
            sort ($fichiers);
            foreach ($fichiers as $fichier) {
                $documents .= "<a target='_blank' href='challenge/index/fichierreponse?id=" 
                    . $reponse ['id'] . "&cid=" . $reponse ['club'] . "&nom=$fichier&t=" . time () . "'>$fichier</a>&nbsp;";
            }
            if ($documents != '') {
                $documents = "<p>&nbsp;<b>Document(s) : </b>" . $documents . "</p>\n";
            }
            $saisie = $reponse ['texte'];

            $texte = "$documents<div>$saisie</div>\n";
            return $texte;
        }
    }