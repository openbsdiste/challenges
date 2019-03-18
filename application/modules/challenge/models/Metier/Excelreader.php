<?php
    class Challenge_Model_Metier_Excelreader {
        protected $_chalInfos;
        protected $_options;
        protected $_reader;
        protected $_objExcel;
        protected $_sheet;
        protected $_clubInfos;

        protected function _getReaderType ($filename, &$readerType) {
            $ok = true;
            $ext = strtolower (substr ($filename, -4));
            if ($ext == '.xls') {
                $readerType = 'Excel5';
            } elseif ($ext == 'xlsx') {
                $readerType = 'Excel2007';
            } else {
                $ok = false;
            }
            return $ok;
        }

        protected function _load ($filename, $readerType) {
            $ok = true;
            try {
                if (! is_readable ($filename)) {
                    throw new Exception ();
                }
                $this->_reader = PHPExcel_IOFactory::createReader ($readerType);
                $this->_objExcel = $this->_reader->load ($filename);
                $this->_sheet = $this->_objExcel->getSheet (0);
            } catch (Exception $e) {
                $ok = false;
            }
            return $ok;
        }

        protected function _checkLoadedFile () {
            $proprietes = $this->_objExcel->getProperties ();
            $ok =
                ($proprietes->getTitle () == "Challenge " . $this->_chalInfos ['annee'])
                && ($proprietes->getCreator () == $this->_options ['properties']['setCreator'])
                && (utf8_decode ($proprietes->getSubject ()) == $this->_options ['properties']['setSubject'])
                && (utf8_decode ($proprietes->getDescription ()) == "Challenge organisé par " . $this->_chalInfos ['club'])
                && ($this->_sheet->getCell ('B2')->getValue () == "Réponse au challenge " . $this->_chalInfos ['annee'])
                && ($this->_sheet->getCell ('B3')->getValue () == "Organisé par " . $this->_chalInfos ['club'])
                && ($this->_sheet->getCell ('B5')->getValue () == "Question")
                && ($this->_sheet->getCell ('C5')->getValue () == "Valeur")
                && ($this->_sheet->getCell ('D5')->getValue () == "Titre")
                && ($this->_sheet->getCell ('E5')->getValue () == "Votre réponse")
                && ($this->_sheet->getHighestColumn () == 'F')
                && ($this->_sheet->getHighestRow () > 5)
            ;
            return $ok;
        }

        protected function _getClubInformations () {
            $metier = new Authentification_Model_Metier_Utilisateurs ();
            $this->_clubInfos = $metier->trouve ($this->_sheet->getCell ('A1')->getValue ());
            return (! is_bool ($this->_clubInfos));
        }

        protected function _getChallenge () {
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $arbre = $metierArbre->getArbre ();
            $mquest = $metierQuestions;

            $challenge = array ();
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
                $q = $mquest->getQuestion ($feuille ['noeud']);
                $challenge [] = array (
                    'noeud' => $feuille ['noeud'],
                    'question' => implode ('.', $indices),
                    'titre' => trim ($feuille ['title']),
                    'valeur' => trim ($q ['valeur']),
                    'test' => implode ('.', $indices) . ' ' . trim ($q ['valeur']) . ' ' . trim ($feuille ['title'])
                );
            }
            return $challenge;
        }

        protected function _parseReponses (&$challenge, &$reponses) {
            $parse = array ();
            $listeReponses = array ();
            foreach ($reponses as $k => $v) {
                $test = $v ['question'] . ' ' . $v ['valeur'] . ' ' . $v ['titre'];
                $question = -1;
                foreach ($challenge as $k2 => $v2) {
                    if ($v2 ['test'] == $test) {
                        $question = $k2;
                    }
                }
                if (($question >= 0) && ($challenge [$question]['valeur'] != '')) {
                    $listeReponses [] = $k;
                    if ($v ['reponse'] != '') {
                        $parse [] = array (
                            'noeud' => $challenge [$question]['noeud'],
                            'reponse' => "Import Excel:<br /><br />" . $v ['reponse']
                        );
                    }
                }
                if ($question >= 0) {
                    unset ($challenge [$question]);
                }
            }
            foreach ($listeReponses as $r) {
                unset ($reponses [$r]);
            }
            return $parse;
        }

        protected function _parseProblemes (&$challenge, &$reponses) {
            $parse = array (
                'possibles' => array (),
                'problemes' => array ()
            );
            foreach ($challenge as $v) {
                $parse ['possibles'][] = array (
                    'noeud' => $v ['noeud'],
                    'indication' => $v ['test']
                );
            }
            foreach ($reponses as $v) {
                if ($v ['reponse'] != '') {
                    $parse ['problemes'][] = array (
                        'reponse' => "Import Excel:<br /><br />" . $v ['reponse'],
                        'indication' => $v ['question'] . ' (' . $v ['valeur'] . ') ' . $v ['titre']
                    );
                }
            }
            return $parse;
        }

        public function _getReponses () {
            $reponses = array ();
            $highestRow = $this->_sheet->getHighestRow ();
            for ($i = 6; $i <= $highestRow; $i++) {
                $reponses [] = array (
                    'question' => trim ($this->_sheet->getCell ('B' . $i)->getValue ()),
                    'titre' => trim ($this->_sheet->getCell ('D' . $i)->getValue ()),
                    'valeur' => trim ($this->_sheet->getCell ('C' . $i)->getValue ()),
                    'reponse' => trim ($this->_sheet->getCell ('E' . $i)->getValue ())
                );
            }
            return $reponses;
        }

        public function __construct ($chalInfos) {
            $this->_chalInfos = $chalInfos;
            // Prépare phpexcel... pas top comme méthode
            // mais la fin justifie les moyens (ou pas !)
            new PHPExcel ();

            $fc = Zend_Controller_Front::getInstance ();
            $options = $fc->getParam ('bootstrap')->getOptions ();
            $this->_options = $options ['excel'];
        }

        public function load ($filename) {
            $readerType = '';
            $ok =
                $this->_getReaderType ($filename, $readerType)
                && $this->_load ($filename, $readerType)
                && $this->_checkLoadedFile ()
                && $this->_getClubInformations ()
            ;
            return $ok;
        }

        public function parseChallenge () {
            $challenge = $this->_getChallenge ();
            $reponses = $this->_getReponses ();
            return array (
                'reponses' => $this->_parseReponses ($challenge, $reponses),
                'problemes' => $this->_parseProblemes ($challenge, $reponses)
            );
        }

        public function getClubInformations () {
            return $this->_clubInfos;
        }
   }