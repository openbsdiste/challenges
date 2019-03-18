<?php
	class Authentification_Form_Passe extends FUF_Form {
	    public function init () {
	    	$this->setDescription ('form.passe.titre');
	        
	        $ancien = new Zend_Form_Element_Password ('ancien');
	        $ancien->setLabel ('form.passe.ancien')
                    ->addFilter ('StringTrim')
                    ->setRequired (true);
	        
	        $nouveau = new Zend_Form_Element_Password ('nouveau');
	        $nouveau->setLabel ('form.passe.nouveau')
                    ->addFilter ('StringTrim')
                    ->setRequired (true);

            $encore = new Zend_Form_Element_Password ('encore');
	        $encore->setLabel ('form.passe.encore')
                    ->addFilter ('StringTrim')
                    ->addValidator ('Identical', false, array('token' => 'nouveau'))
                    ->setRequired (true);
            
            $memoire = new Zend_Form_Element_Text ('memoire');
            $memoire->setLabel ('form.passe.memoire')
                    ->setAttrib ('size', '100')
                    ->addFilter ('StringTrim')
                    ->addFilter ('StripTags');

            $submit = new Zend_Form_Element_Submit ('changer');
	        $submit->setLabel ('form.passe.changer')
                    ->setAttrib ('class', 'ui-state-default ui-corner-all');
	        
	        $this->addElements (array (
	            $ancien, $nouveau, $encore, $memoire, $submit
	        ));
	        
	        $this->addDisplayGroup (array ('ancien', 'nouveau', 'encore', 'memoire'), 'contenu');
	        $this->addDisplayGroup (array ('changer'), 'validation');
	    }
	}