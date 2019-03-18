<?php
    class App_View_Helper_Question extends Zend_View_Helper_Abstract {
        protected function _moderateur ($question, $fichiers, $statut) {
            if ($question ['image'] != '') {
                $image = "<img id='imgpreview" . $question ['id'] . "' src='challenge/$statut/image?id=" . $question ['id'] . "&t=" . time () . "' width=300 style='float:left;'/>";
            } else {
                $image = "";
            }
            $documents = "";
            sort ($fichiers);
            foreach ($fichiers as $fichier) {
                $documents .= "<a target='_blank' href='challenge/$statut/fichier?id=" . $question ['id'] . "&nom=" . urlencode ($fichier) . "&t=" . time () . "'>$fichier</a>&nbsp;";
            }
            if ($documents != '') {
                $documents = "<p>&nbsp;<b>Document(s) : </b>" . $documents . "</p>";
            }
            
            return array ($image, $documents);
        }
        
        public function question ($question, $fichiers, $statutChallenge) {
            $statut = ($statutChallenge == Model_Enum_StatutChallenge::ELABORATION)
                ? 'elaboration'
                : 'index'
            ;
            list ($image, $documents) = $this->_moderateur ($question, $fichiers, $statut);
            $saisie = $question ['texte'];
            if ($question  ['information']) {
                $points = "";
            } else {
                $points = "<p>&nbsp;<b>Valeur de la question</b> : " . $question ['valeur'] . "</p>";
            }

            $texte = <<<EOT
<p>
    $image
    <span>
        $points<br />
        $documents
    </span>
</p>
<p style="clear:both;">
    <br />
    $saisie
</p>
EOT;
            
            return $texte;
        }
    }