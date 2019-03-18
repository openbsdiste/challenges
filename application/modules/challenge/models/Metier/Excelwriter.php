<?php
    class Challenge_Model_Metier_Excelwriter {
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
            $this->_sheet->setTitle ($this->_identite->club);
            $this->_sheet->getProtection ()->setPassword ($this->_options ['properties']['setCreator']);
            
            foreach ($this->_options ['protection'] as $v) {
                $this->_sheet->getProtection ()->$v (true);
            }
            
            $this->_sheet->getPageSetup ()->setOrientation (PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            $this->_sheet->getPageSetup ()->setPaperSize (PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            foreach ($this->_options ['colonnes'] as $k => $v) {
                $this->_sheet->getColumnDimension ($k)->setWidth ($v);
            }
        }
        
        protected function _setTitreFeuille () {
            $this->_ligne = 6;
            $this->_sheet->setCellValue ('A1', $this->_identite->id);
            $this->_sheet->setCellValue ('B2', "Réponse au challenge " . $this->_chalInfos ['annee']);
            $this->_sheet->setCellValue ('B3', "Organisé par " . $this->_chalInfos ['club']);
            $this->_sheet->setCellValue ('B5', "Question");
            $this->_sheet->setCellValue ('C5', "Valeur");
            $this->_sheet->setCellValue ('D5', "Titre");
            $this->_sheet->setCellValue ('E5', "Votre réponse");
            $this->_sheet->mergeCells ('B2:E2');
            $this->_sheet->mergeCells ('B3:E3');
            $this->_sheet->freezePane ('A6');
            
            $this->_sheet->getStyle ('A1')->applyFromArray (array (
                'font' => array (
                    'color' => array ('argb' => 'FFFFFFFF')
                )
            ));
            
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
            $this->_sheet->getStyle ('B5')->applyFromArray ($styleTitreTable);
            $this->_sheet->getStyle ('C5')->applyFromArray ($styleTitreTable);
            $this->_sheet->getStyle ('D5')->applyFromArray ($styleTitreTable);
            $this->_sheet->getStyle ('E5')->applyFromArray ($styleTitreTable);
        }
        
        protected function _addChallengeLigne ($indice, $valeur, $titre) {
            $this->_sheet->setCellValue ('B' . $this->_ligne, ' ' . $indice);
            $this->_sheet->setCellValue ('C' . $this->_ligne, $valeur);
            $this->_sheet->setCellValue ('D' . $this->_ligne, $titre);
            $styleCellule = array (
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array ('argb' => 'FFF0F0F0'),
                ),
            );
            if ($valeur != '') {
                $this->_sheet->getStyle ('E' . $this->_ligne)->getProtection ()->setLocked (PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
                $this->_sheet->getStyle ('B' . $this->_ligne . ':D' . $this->_ligne)->applyFromArray ($styleCellule);
                $this->_sheet->getRowDimension ($this->_ligne)->setRowHeight (45);
            } else {
                $this->_sheet->getStyle ('B' . $this->_ligne . ':E' . $this->_ligne)->applyFromArray ($styleCellule);
            }
            $styleBordure = array (
                'borders' => array (
                    'outline' => array (
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array ('argb' => 'FF000000'),
                    ),
                ),
            );
            $this->_sheet->getStyle ('B' . $this->_ligne)->applyFromArray ($styleBordure);
            $this->_sheet->getStyle ('C' . $this->_ligne)->applyFromArray ($styleBordure);
            $this->_sheet->getStyle ('D' . $this->_ligne)->applyFromArray ($styleBordure);
            $this->_sheet->getStyle ('E' . $this->_ligne)->applyFromArray ($styleBordure);

            $this->_ligne++;
        }
        
        public function __construct ($chalInfos, $identite) {
            $this->_chalInfos = $chalInfos;
            $this->_identite = $identite;
            
            $this->_phpExcel = new PHPExcel ();

            $fc = Zend_Controller_Front::getInstance ();
            $options = $fc->getParam ('bootstrap')->getOptions ();
            $this->_options = $options ['excel'];
            
            $this->_setFeuille ();
            $this->_setTitreFeuille ();
        }
        
        public function setChallenge () {
            $metierArbre = new Challenge_Model_Metier_Arbre ($this->_chalInfos ['id']);
            $metierQuestions = new Challenge_Model_Metier_Questions ($this->_chalInfos);
            $arbre = $metierArbre->getArbre ();
            $mquest = $metierQuestions;

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
                $this->_addChallengeLigne (implode ('.', $indices), $q ['valeur'], $feuille ['title']);
            }
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