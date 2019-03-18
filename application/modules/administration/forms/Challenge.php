<?php
	class Administration_Form_Challenge extends FUF_Form {
	    public function init () {
	    	$this->setDescription ('form.challenge.titre');
	    	
	        $annee = new Zend_Form_Element_Text ('annee');
	        $annee->setLabel ('form.challenge.annee')
                    ->addFilter (new Zend_Filter_StripTags ())
                    ->addFilter (new Zend_Filter_StringTrim ())
                    ->addValidator (new Zend_Validate_Regex ('/^20\d\d$/'))
                    ->setAttrib ('size', 4)
                    ->setAttrib ('maxlength', 4)
                    ->setRequired (true);
	        
            $metier = new Authentification_Model_Metier_Utilisateurs ();
	        $organisateur = new Zend_Form_Element_Select ('organisateur');
	        $organisateur->setLabel ('form.challenge.organisateur')
                    ->setMultiOptions ($metier->getListeClubs ())
                    ->setRequired (true);
	        
	        $submit = new Zend_Form_Element_Submit ('creer');
	        $submit->setLabel ('form.challenge.creer')
                    ->setAttrib ('class', 'ui-state-default ui-corner-all');
	        
	        $this->addElements (array (
	            $annee, $organisateur, $submit
	        ));
	        
	        $this->addDisplayGroup (array ('annee', 'organisateur'), 'contenu');
	        $this->addDisplayGroup (array ('creer'), 'validation');
	    }
	}