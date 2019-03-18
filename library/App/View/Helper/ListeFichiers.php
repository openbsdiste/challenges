<?php
    class App_View_Helper_ListeFichiers extends Zend_View_Helper_Abstract {
        public function listeFichiers ($id, $fichiers, $ctrl = 'reponse', $prefix = '', $delete = true) {
            sort ($fichiers);
            $texte = "<ul>\n";
            foreach ($fichiers as $fichier) {
                $eFichier = urlencode ($fichier);
                $texte .= '<li>';
                if ($delete) {
                    $texte .= "<img filename='$eFichier' qid='$id' src='mediatheque/images/close.png' width='16px' class='deletefile$prefix'/>";
                }
                $texte .= "&nbsp;<span><a href='challenge/$ctrl/fichier$prefix?id=$id&nom=$eFichier' target='_blank'>$fichier</a></span>";
                $texte .= "</li>\n";
                if ($delete) {
                    if ($prefix == '') {
                        $texte .= "<script>DeleteDocuments.initialize();</script>\n";
                    } else {
                        $texte .= "<script>DeleteDocumentsReponse.initialize();</script>\n";
                    }
                }
            }
            $texte .= "</ul>\n";
            return $texte;
        }
    }