<?php
	class Authentification_Form_Auth extends FUF_Form {
	    public function init () {
	    	$this->setDescription ('form.auth.titre');
	    	
	        $login = new Zend_Form_Element_Text ('login');
	        $login->setLabel ('form.auth.login')
                    ->addFilter (new Zend_Filter_StripTags ())
                    ->addFilter (new Zend_Filter_StringToUpper ())
                    ->addFilter (new Zend_Filter_StringTrim ())
                    ->setRequired (true);
	        
	        $password = new Zend_Form_Element_Password ('password');
	        $password->setLabel ('form.auth.password')
                    ->addFilter (new Zend_Filter_StringTrim ())
                    ->setRequired (true);
	        
	        $submit = new Zend_Form_Element_Submit ('connexion');
	        $submit->setLabel ('form.auth.connexion')
                    ->setAttrib ('class', 'ui-state-default ui-corner-all');
	        
	        $this->addElements (array (
	            $login, $password, $submit
	        ));
	        
	        $this->addDisplayGroup (array ('login', 'password'), 'contenu');
	        $this->addDisplayGroup (array ('connexion'), 'validation');
	    }
	}