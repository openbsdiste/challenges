<?php
    class Challenge_Model_Metier_Noteswriter {
        protected $_phpExcel;
        protected $_options;
        protected $_chalInfos;
        protected $_identite;
        protected $_sheet;
        protected $_ligne;
        
        protected function _setFeuille () {
            foreach ($this->_options ['properties'] as $k => $v) {
                $this->_phpExcel->getProperties ()->$k ($v);
            }
            $this->_phpExcel->getProperties ()->setTitle ("Challenge " . $this->_chalInfos ['annee']);
            $this->_phpExcel->getProperties ()->setDescription ("Challenge organisé par " . $this->_chalInfos ['club']);
            
            $this->_phpExcel->setActiveSheetIndex (0);
            $this->_sheet = $this->_phpExcel->getActiveSheet ();
            $this->_sheet->setTitle ('Notes');            
            
            $this->_sheet->getPageSetup ()->setOrientation (PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            $this->_sheet->getPageSetup ()->setPaperSize (PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        }
        
        protected function _remplitFeuille ($challenge) {
            $largeur = count ($challenge [0]) + 1;
            $this->_ligne = 5;
            $this->_sheet->setCellValue ('B2', "Réponse au challenge " . $this->_chalInfos ['annee']);
            $this->_sheet->setCellValue ('B3', "Organisé par " . $this->_chalInfos ['club']);
            $this->_sheet->mergeCellsByColumnAndRow (1, 2, $largeur - 1, 2);
            $this->_sheet->mergeCellsByColumnAndRow (1, 3, $largeur - 1, 3);
                        
            $styleTitre = array (
                'font' => array (
                    'bold' => true,
                    'size' => 24,
                    'color' => array ('argb' => 'FF0000FF')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $this->_sheet->getStyle ('B2')->applyFromArray ($styleTitre);
            $this->_sheet->getStyle ('B3')->applyFromArray ($styleTitre);
            
            $styleTitreTable = array (
                'font' => array (
                    'bold' => true,
                    'color' => array ('argb' => 'FFFFFFFF'),
                ),
                'borders' => array (
                    'outline' => array (
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array ('argb' => 'FF000000'),
                    ),
                ),
                'fill' => array (
                    'type' => PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY,
                    'color' => array ('argb' => 'FF000000'),
                ),
            );

            $styleCellule = array (
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array ('argb' => 'FFF0F0F0'),
                ),
            );
            
            $styleBordure = array (
                'borders' => array (
                    'outline' => array (
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array ('argb' => 'FF000000'),
                    ),
                ),
            );

            foreach ($challenge as $ligne) {
                foreach ($ligne as $k => $v) {
                    $this->_sheet->setCellValueByColumnAndRow ($k + 1, $this->_ligne, $v);
                    $this->_sheet->getStyleByColumnAndRow ($k + 1, $this->_ligne)->applyFromArray ($styleCellule);
                    if ($this->_ligne < 7) {
                        $this->_sheet->getStyleByColumnAndRow ($k + 1, $this->_ligne)->applyFromArray ($styleTitreTable);
                    } else {
                        $this->_sheet->getStyleByColumnAndRow ($k + 1, $this->_ligne)->applyFromArray ($styleBordure);
                    }
                }
                $this->_ligne++;
            }
        }
        
        public function __construct ($chalInfos, $identite) {
            $this->_chalInfos = $chalInfos;
            $this->_identite = $identite;
            
            $this->_phpExcel = new PHPExcel ();

            $fc = Zend_Controller_Front::getInstance ();
            $options = $fc->getParam ('bootstrap')->getOptions ();
            $this->_options = $options ['excel'];
            
            $this->_setFeuille ();
        }
        
        public function setChallenge () {
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $metierParticipants = new Challenge_Model_Metier_Participants ($this->_chalInfos);
            $arbre = $metierArbre->getArbre ();
            $mquest = $metierQuestions;
            $resultats = $metierParticipants->getResultats ();
            foreach ($resultats as $k => $resultat) {
                $metierReponses = new Challenge_Model_Metier_Reponses ($this->_chalInfos, $resultat ['id']);
                $resultats [$k]['notes'] = $metierReponses->getNotesClub ();
            }
            
            $questions = array ();
            $total = 0;
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
                $questions [$feuille ['noeud']] = array (
                    'num'   => implode ('.', $indices),
                    'titre' => $feuille ['title'],
                    'val'   => $q ['valeur']
                );
                if ($q ['valeur'] != '') {
                    $total += floatval ($q ['valeur']);
                }
            }
            
            $challenge = array ();
            $ligne = array ('Classement', 'Club', 'Total', 'Total/20');
            foreach ($questions as $q) {
                if ($q ['val'] != '') {
                    $ligne [] = $q ['num'];
                }
            }
            $challenge [] = $ligne;
            
            $ligne = array ('', '', $total, 20);
            foreach ($questions as $q) {
                if ($q ['val'] != '') {
                    $ligne [] = $q ['val'];
                }
            }
            $challenge [] = $ligne;
            
            foreach ($resultats as $k => $r) {
                $ligne = array ($k + 1, $r ['club'], $r ['note'], round ($r ['note'] / $total * 2000) / 100);
                foreach ($questions as $n => $q) {
                    if ($q ['val'] != '') {
                        $ligne [] = (isset ($r ['notes'][$n])) ? $r ['notes'][$n] : 0;
                    }
                }
                $challenge [] = $ligne;
            }
            $this->_remplitFeuille ($challenge);
        }
        
        public function renderExcel5 ($nom) {
            header ('Content-Type: application/xls');
            header ('Content-Disposition: attachment;filename="' . $nom . '"');
            header ('Cache-Control: max-age=0');

            $writer = PHPExcel_IOFactory::createWriter ($this->_phpExcel, 'Excel5');
            $writer->save ('php://output'); 
        }

        public function renderExcel2007 ($nom) {
            header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header ('Content-Disposition: attachment;filename="' . $nom . '"');
            header ('Cache-Control: max-age=0');

            $writer = PHPExcel_IOFactory::createWriter ($this->_phpExcel, 'Excel2007');
            $writer->save ('php://output'); 
        }
    }